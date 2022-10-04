import { useIsFocused, useNavigation, useRoute } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useLayoutEffect } from "react"

import DeferredLoading from "components/DeferredLoading"

import { useAuth } from "providers/Auth"

import { EntryStackParamList } from "./EntryStackParamList"

type CurrentScreenNavigationProp = StackNavigationProp<EntryStackParamList, any>

export const LoggedInRedirectMiddleware = ({ children }) => {
  const { loggedIn, isLoading } = useAuth()
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const route = useRoute()
  const focused = useIsFocused()

  useLayoutEffect(() => {
    if (loggedIn && focused && !isLoading) {
      navigation.reset({
        index: 0,
        routes: [{ name: "Main" }],
      })
    }
  }, [loggedIn, navigation, route, focused, isLoading])

  if (loggedIn) {
    return <DeferredLoading />
  }

  // We pass through even when auth isLoading is true. This runs when !loggedIn where isLoading is irrelevant
  return <>{children}</>
}

export const withLoggedInRedirectMiddleware = (Component) => () =>
  (
    <LoggedInRedirectMiddleware>
      <Component />
    </LoggedInRedirectMiddleware>
  )
