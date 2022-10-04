import React from "react"
import { useWindowDimensions } from "react-native"
import { Div } from "react-native-magnus"

import Image from "components/Image"
import Text from "components/Text"

export default () => {
  const { width: screenWidth } = useWindowDimensions()

  return (
    <Div bg="white" justifyContent="center" alignItems="center" flex={1}>
      <Image
        source={require("assets/icon_empty_cart.jpg")}
        width={0.3 * screenWidth}
        scalable
      />
      <Text
        testID="noProductText"
        mt={30}
        mb={5}
        fontSize={14}
        textAlign="center"
      >
        Cart is Empty
      </Text>
      <Text testID="noProductText" textAlign="center" color="grey">
        Look like you haven't made your choice yet
      </Text>
    </Div>
  )
}
