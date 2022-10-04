import Case from "case"
import React, { useState } from "react"
import { Button, Div, Icon } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import { Select } from "components/Select"
import Text from "components/Text"

import { ReportableType, ReportableTypeList } from "api/generated/enums"

import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

type PropTypes = {
  value: string
  onSelect: (value: ReportableType) => void
  disabled?: boolean
}

export default ({ value, onSelect, disabled = false }: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const data = ReportableTypeList.filter((type) => type !== "COMPANY")

  const clearButton = () => {
    if (!!value) {
      return (
        <Button
          ml="auto"
          bg="white"
          p={10}
          my={-10}
          onPress={() => onSelect(null)}
        >
          <Icon name="close" color="grey" fontSize={15} />
        </Button>
      )
    }
  }

  return (
    <>
      <Div row alignItems="center">
        <Button
          flex={1}
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
          suffix={clearButton()}
        >
          {!!value ? Case.title(value) : "Click to select a Type"}
        </Button>
      </Div>

      <Select
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title="Tipe Report"
        message="Please select a Type"
        data={data || []}
        ListFooterComponent={() => <EndOfList />}
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
