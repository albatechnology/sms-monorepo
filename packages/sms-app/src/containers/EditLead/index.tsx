import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
  RouteProp,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { ScrollView } from "react-native"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import LeadForm from "forms/LeadForm"

import { customErrorHandler } from "api/errors"
import useLeadById from "api/hooks/lead/useLeadById"
import useLeadEditMutation from "api/hooks/lead/useLeadEditMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "EditLead">
type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "EditLead">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [editLead] = useLeadEditMutation()

  const leadId = route?.params?.id ?? -1
  if (leadId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: leadData }],
    meta,
  } = useMultipleQueries([
    useLeadById(
      leadId,
      customErrorHandler({
        404: () => {
          toast("Lead tidak ditemukan")
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
        <LeadForm
          isEditing
          initialValues={{ ...leadData, customerId: leadData.customer.id }}
          submitButtonText="Edit"
          onSubmit={async (data) => {
            await editLead(
              {
                id: leadId,
                type: data.type,
                label: data.label,
                customerId: data.customerId,
              },
              (x) =>
                x.then(() => {
                  navigation.reset({
                    index: 0,
                    routes: [{ name: "CustomerList" }],
                  })
                }),
            )
          }}
        />
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
