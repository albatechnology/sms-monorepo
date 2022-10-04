import DateTimePicker from "@react-native-community/datetimepicker"
import { format, sub, startOfDay, isValid } from "date-fns"
import React, { useEffect, useState } from "react"
import { Platform } from "react-native"
import { useColorScheme } from "react-native-appearance"
import { Button, Div, Icon } from "react-native-magnus"
import Modal from "react-native-modal"

import Text from "components/Text"

type PropTypes = {
  placeholder
  value
  onSelect: (value: Date) => void
  disabled?: boolean
  minimumDate?: Date
  maximumDate?: Date
  reset?: boolean
}

export default ({
  placeholder = "",
  value,
  onSelect,
  disabled = false,
  minimumDate = sub(new Date(), { months: 12 }),
  maximumDate = new Date(),
  reset = true,
}: PropTypes) => {
  const [showDatePicker, setShowDatePicker] = useState(false)

  const todayDate = new Date()

  const dateObject = !!value ? new Date(value) : null

  const [iosVal, setIosVal] = useState(todayDate)

  useEffect(() => {
    if (!!value) {
      setIosVal(new Date(value))
    }
  }, [value])

  const onChange = (event, selectedDate) => {
    setShowDatePicker(false)
    if (isValid(selectedDate)) {
      onSelect(startOfDay(selectedDate))
    } else {
      onSelect(selectedDate)
    }
  }

  const clearButton = () => {
    if (dateObject) {
      return (
        <Button bg="white" p={10} my={-10} onPress={() => onSelect(null)}>
          <Icon name="close" color="grey" fontSize={16} />
        </Button>
      )
    }
  }

  if (Platform.OS === "android") {
    return (
      <>
        <Button
          block
          borderWidth={1}
          bg="white"
          color="primary"
          fontSize={11}
          py={13}
          borderColor="grey"
          justifyContent="space-between"
          onPress={() => setShowDatePicker(true)}
          disabled={disabled}
          suffix={!!reset ? clearButton() : null}
        >
          {dateObject ? format(dateObject, "dd MMM yyyy") : placeholder}
        </Button>
        {!!showDatePicker && (
          <DateTimePicker
            value={dateObject || todayDate}
            mode="date"
            display="calendar"
            onChange={onChange}
            minimumDate={minimumDate}
            maximumDate={maximumDate}
            style={{
              opacity: 1,
              backgroundColor: "white",
            }}
          />
        )}
      </>
    )
  }

  if (Platform.OS === "ios") {
    const colorScheme = useColorScheme()
    return (
      <>
        {showDatePicker ? (
          <Modal
            useNativeDriver
            isVisible={showDatePicker}
            animationIn="slideInUp"
            animationOut="slideOutDown"
            onBackdropPress={() => setShowDatePicker(false)}
          >
            <Div p={20} bg={colorScheme === "dark" ? "primary" : "white"}>
              <DateTimePicker
                value={iosVal}
                mode="date"
                display="inline"
                onChange={(e, val) => setIosVal(val)}
                minimumDate={minimumDate}
                maximumDate={maximumDate}
                style={{
                  opacity: 1,
                }}
              />
              <Button
                onPress={() => {
                  onSelect(iosVal)
                  setShowDatePicker(false)
                }}
                bg={colorScheme === "dark" ? "white" : "primary"}
                mt={30}
                mb={10}
                px={20}
                alignSelf="center"
                w={"100%"}
              >
                <Text
                  fontWeight="bold"
                  color={colorScheme === "dark" ? "primary" : "white"}
                >
                  Apply
                </Text>
              </Button>
            </Div>
          </Modal>
        ) : (
          <Button
            block
            borderWidth={1}
            bg="white"
            color="primary"
            fontSize={11}
            py={13}
            borderColor="grey"
            justifyContent="space-between"
            onPress={() => setShowDatePicker(true)}
            disabled={disabled}
            suffix={!!reset ? clearButton() : null}
          >
            {dateObject ? format(dateObject, "dd MMM yyyy") : placeholder}
          </Button>
        )}
      </>
    )
  }
}
