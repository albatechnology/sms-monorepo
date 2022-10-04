import React from "react"
import { Div, DivProps } from "react-native-magnus"

import Text from "components/Text"

import { COLOR_DISABLED } from "helper/theme"

type PropTypes = DivProps & {
  current: number
  target: number
  color: string
}

export default ({ current, target, color, ...rest }: PropTypes) => {
  // Cap max percentage at 100%
  const cPercentage = (current / Math.max(target, current)) * 100
  const tPercentage = 100 - cPercentage

  if (target === 0) {
    return (
      <Div row>
        <Text w="100%" textAlign="center" color="grey">
          No Target
        </Text>
      </Div>
    )
  }

  return (
    <Div h={8} row {...rest}>
      <Div w={`${cPercentage}%`} bg={color} />
      <Div w={`${tPercentage}%`} bg={COLOR_DISABLED} />
    </Div>
  )
}
