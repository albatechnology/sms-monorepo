import { useNavigation } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { useWindowDimensions, ScrollView } from "react-native"
import { Div } from "react-native-magnus"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Image from "components/Image"
import Text from "components/Text"

import { useAuth } from "providers/Auth"

import LoginForm from "forms/LoginForm"

import useLoginMutation from "api/hooks/user/useLoginMutation"

import { EntryStackParamList } from "Router/EntryStackParamList"

import { responsive } from "helper"
import Languages from "helper/languages"
import { COLOR_PRIMARY } from "helper/theme"

type CurrentScreenNavigationProp = StackNavigationProp<
  EntryStackParamList,
  "Login"
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const { height: screenHeight } = useWindowDimensions()

  const [login] = useLoginMutation()
  const { onLogin } = useAuth()

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <ScrollView
        contentContainerStyle={{
          flexGrow: 1,
          justifyContent: "center",
          backgroundColor: COLOR_PRIMARY,
        }}
      >
        <Div w="100%" justifyContent="center" alignItems="center">
          <Image
            style={[{ marginVertical: responsive(60) }]}
            source={require("assets/Logo.png")}
            width={screenHeight * 0.3}
            scalable
            resizeMode="contain"
          />
          <Text fontSize={16} fontWeight="bold" color="white" mb={5}>
            Welcome To MOVES
          </Text>
          <Text color="white" mb={20}>
            Enter your ID to login
          </Text>
          <LoginForm
            onSubmit={(values) => {
              return login(
                { email: values.email, password: values.password },
                (x) =>
                  x.then((res) => {
                    toast(Languages.LoginSuccess)
                    onLogin(res.data)
                    navigation.reset({
                      index: 0,
                      routes: [{ name: "Main" }],
                    })

                    return res
                  }),
              )
            }}
          />
        </Div>
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
