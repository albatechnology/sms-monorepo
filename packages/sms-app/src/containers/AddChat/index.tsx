import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { ScrollView } from "react-native"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"

import ChatForm from "forms/ChatForm"

import useQATopicCreateMutation from "api/hooks/qaTopic/useQATopicCreateMutation"

import { ChatStackParamList, MainTabParamList } from "Router/MainTabParamList"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ChatStackParamList, "AddChat">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [createChat] = useQATopicCreateMutation()

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
        <ChatForm
          onSubmit={async (data) => {
            await createChat(
              {
                subject: data.subject,
                users: data.users,
              },
              (x) =>
                x.then(() => {
                  navigation.reset({
                    index: 0,
                    routes: [{ name: "Chat" }],
                  })
                }),
            )
          }}
        />
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
