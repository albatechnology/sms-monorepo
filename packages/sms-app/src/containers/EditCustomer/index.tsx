import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  RouteProp,
  useRoute,
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { ScrollView } from "react-native"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import CustomerForm from "forms/CustomerForm"

import { customErrorHandler } from "api/errors"
import useCustomerById from "api/hooks/customer/useCustomerById"
import useCustomerEditMutation from "api/hooks/customer/useCustomerEditMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "EditCustomer">

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "EditCustomer">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [editCustomer] = useCustomerEditMutation()

  const customerId = route?.params?.id ?? -1
  if (customerId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: customerData }],
    meta,
  } = useMultipleQueries([
    useCustomerById(
      customerId,
      {},
      customErrorHandler({
        404: () => {
          toast("Customer tidak ditemukan")
          if (navigation.canGoBack()) {
            navigation.goBack()
          } else {
            navigation.navigate("Dashboard")
          }
        },
      }),
    ),
  ] as const)

  const { isError, isLoading, isFetching, refetch } = meta

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <ScrollView
        contentContainerStyle={[
          {
            flexGrow: 1,
            alignItems: "center",
          },
        ]}
      >
        <CustomerForm
          initialValues={customerData}
          customerId={customerData?.id}
          onSubmit={async (data) => {
            await editCustomer(
              {
                id: customerId,
                firstName: data.firstName,
                lastName: data.lastName,
                dateOfBirth: data.dateOfBirth,
                email: data.email,
                phone: data.phone,
                description: data.description,
                title: data.title,
                defaultAddressId: data.defaultAddressId,
              },
              (x) =>
                x.then(() => {
                  navigation.goBack()
                }),
            )
          }}
          submitButtonText="Edit"
        />
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
