import Case from "case"
import React, { useState } from "react"
import { Button, Div, Icon } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import { Select } from "components/Select"
import Text from "components/Text"

import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

type PropTypes<EnumType> = {
  enumList: EnumType[]
  value: string
  onSelect: (value: EnumType) => void
  disabled?: boolean
  title?: string
  entityName?: string
}

export default function <EnumType extends string>({
  enumList,
  value,
  onSelect,
  disabled = false,
  title = "Type",
  entityName = "Type",
}: PropTypes<EnumType>) {
  const [visible, setVisible] = useState(false)

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
          {!!value ? Case.title(value) : `Click to select a ${entityName}`}
        </Button>
      </Div>

      <Select
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title={title}
        message={`Please select a ${entityName}`}
        data={enumList || []}
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
