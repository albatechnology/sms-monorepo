import React, { useState, useEffect } from "react"
import { Input } from "react-native-magnus"

import { responsive } from "helper"

type PropTypes = {
  quantity: number
  onChange: (newQuantity: number) => void
}

export default ({ quantity, onChange, ...rest }: PropTypes) => {
  const [value, setValue] = useState<string>(quantity.toString())

  // Update on change somewhere else (eg: button click)
  useEffect(() => {
    if (value !== quantity.toString()) {
      setValue(quantity.toString())
    }
  }, [quantity])

  const onChangeText = (text) => {
    setValue(text)
    const newVal = parseInt(text, 10)

    // Update to store only if it is valid
    if (Number.isSafeInteger(newVal)) {
      onChange(newVal)
    }
  }

  // Cleanup user input
  const onBlur = () => {
    const newVal = parseInt(value, 10)

    if (!Number.isSafeInteger(newVal)) {
      setValue(quantity.toString())
    } else {
      setValue(newVal.toString())
    }
  }

  return (
    <Input
      onChangeText={onChangeText}
      onBlur={onBlur}
      keyboardType="numeric"
      value={`${value}`}
      w={50}
      h={responsive(30)}
      py={5}
      fontSize={12}
      textAlign="center"
      borderWidth={0}
      borderTopWidth={0.8}
      borderBottomWidth={0.8}
      borderColor="grey"
      {...rest}
    />
  )
}
