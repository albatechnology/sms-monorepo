import React from "react"
import { ActivityIndicator } from "react-native"
import { Div } from "react-native-magnus"

import { COLOR_PRIMARY } from "helper/theme"

type Props = {}

export default function Loading(props: Props) {
  return (
    <Div flex={1} my={20} justifyContent="center">
      <ActivityIndicator size="large" color={COLOR_PRIMARY} />
    </Div>
  )
}
