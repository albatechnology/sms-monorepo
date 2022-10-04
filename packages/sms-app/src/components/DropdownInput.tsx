import Case from "case"
import React, { useState } from "react"
import { Button } from "react-native-magnus"

import { Select } from "components/Select"
import Text from "components/Text"

import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

type PropTypes = {
  data: any[]
  title: string
  message: string
  value: string
  onSelect: (value) => void
  disabled?: boolean
}

export default ({
  data = [],
  title = "",
  message = "",
  value,
  onSelect,
  disabled = false,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)
  return (
    <>
      <Button
        block
        borderWidth={1}
        bg="white"
        color={value ? "primary" : COLOR_PLACEHOLDER}
        fontSize={11}
        py={13}
        borderColor="grey"
        justifyContent="flex-start"
        onPress={() => setVisible(!visible)}
        disabled={disabled}
      >
        {!!value ? Case.title(value.toString()) : message}
      </Button>
      <Select
        onSelect={onSelect}
        visible={visible}
        setVisible={setVisible}
        value={value}
        title={title}
        message={message}
        data={data}
        renderItem={(item, index) => (
          <Select.Option
            value={item}
            py={20}
            pb={10}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            <Text>{Case.title(item)}</Text>
          </Select.Option>
        )}
      />
    </>
  )
}
