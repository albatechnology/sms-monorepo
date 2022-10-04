import * as Clipboard from "expo-clipboard"
import React, { useMemo } from "react"
import { TouchableOpacity } from "react-native"
import { Div, DivProps, Icon } from "react-native-magnus"

import Text from "components/Text"

type PropTypes = DivProps & {
  title: string
  data?: string | React.ReactNode
  length?: string
}

export default function InfoBlock({ title, data, length, ...rest }: PropTypes) {
  const copyToClipboard = async () => {
    await Clipboard.setString(data)
    toast("Orlan number copied successfully")
  }
  const dataRender = useMemo(() => {
    if (data === null || data === undefined) {
      return <Text>-</Text>
    }
    if (title === "Orlan Number") {
      return (
        <Div row>
          <TouchableOpacity onPress={copyToClipboard}>
            <Icon fontSize={14} name="copy" mr={5} fontFamily="Ionicons" />
          </TouchableOpacity>
          <Text>{data}</Text>
        </Div>
      )
    }
    if (typeof data === "string" || typeof data === "number") {
      return <Text>{data}</Text>
    } else {
      return data
    }
  }, [data])
  return (
    <Div
      row
      p={16}
      justifyContent="space-between"
      alignItems="center"
      borderBottomWidth={0.8}
      borderBottomColor="grey"
      {...rest}
    >
      <Text>{title}</Text>

      {!length ? (
        dataRender
      ) : (
        <Text w={200} textAlign="right">
          {dataRender}
        </Text>
      )}
    </Div>
  )
}
