import React, { useState } from "react"
import { Button } from "react-native-magnus"

import { Select } from "components/Select"
import Text from "components/Text"

import countryList from "helper/data/countries"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

type PropTypes = {
  value: string
  onSelect: (value) => void
  disabled?: boolean
}

export default ({ value, onSelect, disabled }: PropTypes) => {
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
        {!!value ? value.toString() : "Please select a country"}
      </Button>
      <Select
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title={"Country"}
        message={"Please select a country"}
        data={countryList}
        renderItem={(item, index) => (
          <Select.Option
            value={item}
            py={20}
            pb={10}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            <Text>{item}</Text>
          </Select.Option>
        )}
      />
    </>
  )
}
