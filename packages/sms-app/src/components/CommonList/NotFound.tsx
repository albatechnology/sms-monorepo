import React from "react"
import { useWindowDimensions } from "react-native"
import { Div } from "react-native-magnus"

import Image from "components/Image"
import Text from "components/Text"

export default () => {
  const { width: screenWidth } = useWindowDimensions()
  return (
    <Div alignItems="center" my={20}>
      <Image
        source={require("assets/icon_not_found.png")}
        width={0.4 * screenWidth}
        scalable
      />
      <Text
        testID="noProductText"
        mt={30}
        mb={5}
        fontSize={14}
        textAlign="center"
      >
        Sorry Search Not Found
      </Text>
    </Div>
  )
}
