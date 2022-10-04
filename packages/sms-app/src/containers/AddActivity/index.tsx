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

import ActivityForm from "forms/ActivityForm"

import useActivityCreateMutation from "api/hooks/activity/useActivityCreateMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "AddActivity">

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "AddActivity">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const customerId = route?.params?.customerId ?? -1
  const leadId = route?.params?.leadId ?? -1
  if (customerId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const [createActivity] = useActivityCreateMutation()

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1, backgroundColor: "#FFF" }}>
      <ScrollView
        contentContainerStyle={[
          {
            flexGrow: 1,
            alignItems: "center",
          },
        ]}
      >
        <ActivityForm
          onSubmit={async (data) => {
            return createActivity(
              {
                followUpMethod: data.followUpMethod,
                status: data.status,
                brandIds: data.brandIds,
                estimatedValue: data.estimatedValue,
                feedback: data.feedback,
                interiorDesign: data.interiorDesign,
                reminderDateTime: data.reminderDateTime,
                reminderNote: data.reminderNote,
                leadId: leadId,
              },
              (x) =>
                x.then((res) => {
                  if (data.status === "HOT") {
                    toast(
                      "Harap untuk segera membuat dan mengirimkan quotation kepada customer",
                    )
                  } else {
                    toast("Activity berhasil dibuat")
                  }
                  navigation.goBack()
                }),
            )
          }}
        />
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
