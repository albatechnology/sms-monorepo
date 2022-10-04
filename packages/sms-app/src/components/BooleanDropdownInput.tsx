import React, { useState } from "react"
import { Button, Div } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import { Select } from "components/Select"
import Text from "components/Text"

import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

type PropTypes = {
  value: boolean | null
  onSelect: (value: boolean | null) => void
  disabled?: boolean
}

const booleanOptions = [
  { label: "All", value: null },
  { label: "Yes", value: true },
  { label: "No", value: false },
]

export default ({ value, onSelect, disabled = false }: PropTypes) => {
  const [visible, setVisible] = useState(false)

  return (
    <>
      <Div row alignItems="center">
        <Button
          flex={1}
          block
          borderWidth={1}
          bg="white"
          color={value !== null ? "primary" : COLOR_PLACEHOLDER}
          fontSize={11}
          py={13}
          borderColor="grey"
          justifyContent="flex-start"
          onPress={() => setVisible(!visible)}
          disabled={disabled}
        >
          {booleanOptions.find((x) => x.value === value)?.label ?? "All"}
        </Button>
      </Div>

      <Select
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title=""
        message="Please select a value"
        data={booleanOptions || []}
        ListFooterComponent={() => <EndOfList />}
        renderItem={(item, index) => (
          <Select.Option
            value={item.value}
            py={20}
            pb={10}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            <Text>{item.label}</Text>
          </Select.Option>
        )}
      />
    </>
  )
}
