import React from "react"
import { Div, DivProps, Text } from "react-native-magnus"

type PropTypes = DivProps & {
  children: React.ReactNode
  containerColor: string
  textColor: string
  textStyle?: Object
}

export default ({
  children,
  containerColor,
  textColor,
  textStyle = {},
  ...rest
}: PropTypes) => {
  return (
    <Div bg={containerColor} px={10} py={5} {...rest}>
      <Text
        fontSize={12}
        color={textColor}
        textAlign="center"
        style={textStyle}
      >
        {children}
      </Text>
    </Div>
  )
}
