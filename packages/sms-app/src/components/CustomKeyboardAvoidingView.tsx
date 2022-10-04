import { useHeaderHeight } from "@react-navigation/stack"
import React from "react"
import { KeyboardAvoidingView, StatusBar, Platform } from "react-native"

export default function CustomKeyboardAvoidingView({
  children,
  style,
  additionalOffset = 0,
}) {
  const statusBarHeight = StatusBar.currentHeight
  const headerHeight = useHeaderHeight()

  const android = headerHeight + statusBarHeight + additionalOffset
  const ios = headerHeight

  return (
    <KeyboardAvoidingView
      style={style}
      behavior={Platform.OS === "ios" ? "padding" : "height"}
      keyboardVerticalOffset={Platform.OS === "android" ? android : ios}
    >
      {children}
    </KeyboardAvoidingView>
  )
}
