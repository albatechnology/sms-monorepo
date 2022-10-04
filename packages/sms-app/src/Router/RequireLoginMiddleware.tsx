import { useIsFocused, useNavigation, useRoute } from "@react-navigation/native"
import React, { useLayoutEffect } from "react"

import DeferredLoading from "components/DeferredLoading"

import { useAuth } from "providers/Auth"

export const RequireLoginMiddleware = ({ children }) => {
  const { loggedIn, isLoading } = useAuth()
  const navigation = useNavigation()
  const route = useRoute()
  const focused = useIsFocused()

  useLayoutEffect(() => {
    if (!loggedIn && focused && !isLoading) {
      navigation.reset({
        index: 0,
        routes: [{ name: "Login" }],
      })
    }
  }, [loggedIn, navigation, route, focused, isLoading])

  if (!loggedIn || isLoading) {
    return <DeferredLoading />
  }

  return <>{children}</>
}

export const withRequireLoginMiddleware = (Component) => () =>
  (
    <RequireLoginMiddleware>
      <Component />
    </RequireLoginMiddleware>
  )
