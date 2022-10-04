import { NavigationContainerRef } from "@react-navigation/native"
import React from "react"
import { useRef } from "react"
import { View } from "react-native"
import { Button, Snackbar, SnackbarProps } from "react-native-magnus"

import NetInfoHandler from "components/NetInfoHandler"
// import ForceUpdateHandler from "components/ForceUpdateHandler"
import UpdateChecker from "components/UpdateChecker"

import { Router } from "./Router"
import { PushNotificationHandler } from "./notifications"

export default () => {
  const navigationRef = useRef<NavigationContainerRef>(null)

  const reset = (params: any) => navigationRef.current.reset(params)

  return (
    <View
      style={{
        flexGrow: 1,
      }}
    >
      <Router ref={navigationRef} />
      <Snackbar
        ref={toastRefHandler}
        bg="primary"
        color="white"
        duration={3500}
      />
      <NetInfoHandler />
      {/* <ForceUpdateHandler /> */}
      <UpdateChecker />
      <PushNotificationHandler reset={reset} />
    </View>
  )
}

const toastRefHandler: React.LegacyRef<Snackbar> = (ref) => {
  global.toastRef = ref
  global.toast = (message: string | JSX.Element, config?: SnackbarProps) => {
    const id = ref.show(message, {
      suffix: (
        <Button
          bg="transparent"
          color="teal400"
          onPress={() => toastRef.hide(id)}
        >
          Ok
        </Button>
      ),
      ...config,
    })
    ref.update(id, message, {
      suffix: (
        <Button
          bg="transparent"
          color="teal400"
          onPress={() => toastRef.hide(id)}
        >
          Ok
        </Button>
      ),
      ...config,
    })
    return id
  }
}
