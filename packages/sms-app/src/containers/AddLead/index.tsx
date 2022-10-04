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

import LeadForm from "forms/LeadForm"

import useLeadCreateMutation from "api/hooks/lead/useLeadCreateMutation"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "AddLead">

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "AddLead">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [createLead] = useLeadCreateMutation()
  const { data } = useUserLoggedInData()

  const customerId = route?.params?.customerId ?? null
  const type = route?.params?.type ?? null
  const isUnhandled = route?.params?.isUnhandled ?? false

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
          initialValues={{
            customerId,
            type,
            leadCategoryId: null,
            isUnhandled: data.type !== "SALES" ? isUnhandled : false,
          }}
          onSubmit={async (data) => {
            await createLead(
              {
                type: data.type,
                label: data.label,
                customerId: data.customerId,
                leadCategoryId: data.leadCategoryId,
                isUnhandled: data.isUnhandled,
                interest: data.interest,
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
