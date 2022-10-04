import React from "react"
import { Button, Icon } from "react-native-magnus"

import Text from "components/Text"

import { responsive } from "helper"
import { COLOR_DISABLED } from "helper/theme"

export default ({ navigate }) => {
  return (
    <Button
      block
      p={10}
      bg="white"
      borderWidth={1}
      borderColor={COLOR_DISABLED}
      prefix={
        <Icon
          name="qr-code-sharp"
          color="black"
          fontSize={responsive(20)}
          fontFamily="Ionicons"
          mr={10}
        />
      }
      onPress={navigate}
    >
      <Text ml={10} fontSize={14} fontWeight="bold">
        Stock
      </Text>
    </Button>
  )
}
