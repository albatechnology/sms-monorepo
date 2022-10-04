import React from "react"
import { Button } from "react-native-magnus"

import Image from "components/Image"
import Text from "components/Text"

import { responsive } from "helper"
import { COLOR_DISABLED } from "helper/theme"

export default ({ navigate }) => {
  return (
    <Button
      flex={1}
      p={10}
      bg="white"
      borderWidth={1}
      borderColor={COLOR_DISABLED}
      prefix={
        <Image
          width={responsive(20)}
          scalable
          source={require("assets/icon_cafe.png")}
        />
      }
      onPress={navigate}
    >
      <Text ml={10} fontSize={14} fontWeight="bold">
        Cafe
      </Text>
    </Button>
  )
}
