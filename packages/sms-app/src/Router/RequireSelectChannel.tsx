import { useIsFocused, useNavigation, useRoute } from "@react-navigation/native"
import React, { useLayoutEffect } from "react"

import DeferredLoading from "components/DeferredLoading"

import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

export const RequireSelectChannel = ({ children }) => {
  const { data, isLoading, isFetching } = useUserLoggedInData()
  const navigation = useNavigation()
  const route = useRoute()
  const focused = useIsFocused()

  useLayoutEffect(() => {
    if (data?.channelId === null && focused && !isLoading && !isFetching) {
      navigation.reset({
        index: 0,
        routes: [{ name: "UserSelectChannel" }],
      })
    }
  }, [data, navigation, route, focused, isLoading, isFetching])

  if (data?.channelId === null || isLoading) {
    return <DeferredLoading />
  }

  return <>{children}</>
}

export const withRequireSelectChannelMiddleware = (Component) => () =>
  (
    <RequireSelectChannel>
      <Component />
    </RequireSelectChannel>
  )
