import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { ScrollView } from "react-native"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import AddressForm from "forms/AddressForm"

import { customErrorHandler } from "api/errors"
import useAddressById from "api/hooks/address/useAddressById"
import useAddressEditMutation from "api/hooks/address/useAddressEditMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "EditAddress">
type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "EditAddress">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [editAddress] = useAddressEditMutation()

  const addressId = route?.params?.addressId ?? -1
  if (addressId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: addressData }],
    meta,
  } = useMultipleQueries([
    useAddressById(
      addressId,
      customErrorHandler({
        404: () => {
          toast("Alamat tidak ditemukan")
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
    <CustomKeyboardAvoidingView style={{ flex: 1, backgroundColor: "white" }}>
      <ScrollView
        contentContainerStyle={[
          {
            flexGrow: 1,
            alignItems: "center",
          },
        ]}
      >
        <AddressForm
          initialValues={addressData}
          onSubmit={async (data) => {
            await editAddress(
              {
                id: addressId,
                addressLine1: data.addressLine1,
                addressLine2: data.addressLine2,
                addressLine3: data.addressLine3,
                postcode: data.postcode,
                city: data.city,
                province: data.province,
                phone: data.phone,
                country: data.country,
                type: data.type,
                customerId: addressData.customerId,
              },
              (x) =>
                x.then(() => {
                  navigation.goBack()
                }),
            )
          }}
          submitButtonText={"Edit"}
        />
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
