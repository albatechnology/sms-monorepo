import React from "react"
import { Text, TextProps } from "react-native-magnus"

const TextWrapper: React.FunctionComponent<TextProps> = (props) => {
  const { fontWeight = "normal", fontSize = 12, color = "primary" } = props

  return (
    <Text fontWeight={fontWeight} fontSize={fontSize} color={color} {...props}>
      {props.children}
    </Text>
  )
}

export default TextWrapper
