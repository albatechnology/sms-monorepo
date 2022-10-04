import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  RouteProp,
  useRoute,
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { KeyboardAvoidingView, Platform, ScrollView } from "react-native"

import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import ActivityForm from "forms/ActivityForm"

import { customErrorHandler } from "api/errors"
import useActivityById from "api/hooks/activity/useActivityById"
import useActivityEditMutation from "api/hooks/activity/useActivityEditMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "EditActivity">

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "EditActivity">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [editActivity] = useActivityEditMutation()

  const activityId = route?.params?.id ?? -1
  if (activityId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: activityData }],
    meta,
  } = useMultipleQueries([
    useActivityById(
      activityId,
      customErrorHandler({
        404: () => {
          toast("Activity tidak ditemukan")
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
    <KeyboardAvoidingView
      behavior={Platform.OS === "ios" ? "padding" : "height"}
      style={{ flex: 1, backgroundColor: "#FFF" }}
    >
      <ScrollView
        contentContainerStyle={[
          {
            flexGrow: 1,
            alignItems: "center",
          },
        ]}
      >
        <ActivityForm
          isEditing
          submitButtonText="Edit"
          initialValues={{
            ...activityData,
            brandIds: activityData.brands.map((brand) => brand.id),
          }}
          onSubmit={async (data) => {
            return editActivity(
              {
                id: activityId,
                followUpDatetime: activityData?.followUpDatetime,
                followUpMethod: data.followUpMethod,
                status: data.status,
                brandIds: data.brandIds,
                estimatedValue: data.estimatedValue,
                feedback: data.feedback,
                reminderDateTime: data.reminderDateTime,
                reminderNote: data.reminderNote,
                leadId: activityData?.lead?.id,
              },
              (x) =>
                x.then(() => {
                  navigation.goBack()
                }),
            )
          }}
        />
      </ScrollView>
    </KeyboardAvoidingView>
  )
}
