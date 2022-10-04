import React, { useMemo, useState } from "react"
import { Button } from "react-native-magnus"

import { Select } from "components/Select"
import Text from "components/Text"

import citiesList from "helper/data/cities"
import provincesList, { provinceNameList } from "helper/data/provinces"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

type PropTypes = {
  provinceName?: string
  title: string
  message: string
  value: string
  onSelect: (value) => void
  disabled?: boolean
}

/** If provinceName passed, then it's city. Province otherwise */
export default ({
  provinceName = undefined,
  title = "",
  message = "",
  value,
  onSelect,
  disabled,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const provinceId = useMemo(
    () =>
      provinceName
        ? provincesList.find((x) => x.name === provinceName)?.id
        : undefined,
    [provinceName],
  )
  const addressList = useMemo(
    () =>
      provinceId
        ? citiesList
            .filter((x) => x.provinceId === provinceId)
            .map((x) => x.name)
        : provinceNameList,
    [provinceId],
  )

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
        {!!value ? value.toString() : message}
      </Button>
      <Select
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title={title}
        message={message}
        data={addressList}
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
