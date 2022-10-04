import React from "react"
import { ActivityIndicator } from "react-native"
import { Div, Text } from "react-native-magnus"

export default () => {
  return (
    <Div
      py={10}
      flex={1}
      flexDir="row"
      alignItems="center"
      justifyContent="center"
      borderColor="#CED0CE"
    >
      <ActivityIndicator animating size="small" color="black" />
      <Text ml={10}>Loading more...</Text>
    </Div>
  )
}
