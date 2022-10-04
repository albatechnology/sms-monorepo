import DateTimePicker from "@react-native-community/datetimepicker"
import { sub } from "date-fns"
import React, { useEffect, useState } from "react"
import { Platform } from "react-native"
import { useColorScheme } from "react-native-appearance"
import { Calendar } from "react-native-calendars"
import { Button, Div, Icon } from "react-native-magnus"
import Modal from "react-native-modal"

import Text from "components/Text"

import { clampDate, formatDate, formatDateOnly } from "helper"

type PropTypes = {
  placeholder: string
  value: string | number | Date
  onSelect: (value: Date) => void
  disabled?: boolean
  minimumDate?: Date
  maximumDate?: Date
  pickTime?: boolean
}

export default ({
  placeholder = "",
  value,
  onSelect,
  disabled = false,
  minimumDate = sub(new Date(), { months: 3 }),
  maximumDate = new Date(),
  pickTime = true,
}: PropTypes) => {
  const [showDatePicker, setShowDatePicker] = useState(false)
  const [showTimePicker, setShowTimePicker] = useState(false)

  const todayDate = new Date()

  const dateObject = !!value ? new Date(value) : null

  const [dateTimeVal, setDateTimeVal] = useState(dateObject)

  useEffect(() => {
    if (!!value) {
      setDateTimeVal(new Date(value))
    }
  }, [value])

  const clearButton = () => {
    if (dateObject && !disabled) {
      return (
        <Button bg="white" p={10} my={-10} onPress={() => onSelect(null)}>
          <Icon name="close" color="grey" />
        </Button>
      )
    }

    return null
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
          suffix={clearButton()}
        >
          {dateObject
            ? pickTime
              ? formatDate(dateObject)
              : formatDateOnly(dateObject)
            : placeholder}
        </Button>
        {!!showDatePicker && (
          <DateTimePicker
            value={dateTimeVal || todayDate}
            mode="date"
            display="calendar"
            onChange={(e, val) => {
              console.log(e, val)
              if (!!val) {
                if (pickTime) {
                  setShowDatePicker(false)
                  setDateTimeVal(val)
                  setShowTimePicker(true)
                } else {
                  setShowDatePicker(false)
                  onSelect(clampDate(minimumDate, val, maximumDate))
                }
              } else {
                setShowDatePicker(false)
              }
            }}
            minimumDate={minimumDate}
            maximumDate={maximumDate}
            style={{
              opacity: 1,
              backgroundColor: "white",
            }}
          />
          //   <Modal
          //   useNativeDriver
          //   isVisible={true}
          //   animationIn="slideInUp"
          //   animationOut="slideOutDown"
          //   onBackdropPress={() => setShowDatePicker(false)}
          // >
          //   <Calendar current={'12-07-1999'} onDayPress={(val) => {
          //      if (!!val) {
          //             if (pickTime) {
          //               setShowDatePicker(false)
          //               setDateTimeVal(val.timestamp)
          //               setShowTimePicker(true)
          //             } else {
          //               setShowDatePicker(false)
          //               onSelect(clampDate(minimumDate, val.timestamp, maximumDate))
          //             }
          //           } else {
          //             setShowDatePicker(false)
          //           }
          //   }}
          //     />
          //   </Modal>
        )}
        {!!dateTimeVal && !!showTimePicker && (
          <DateTimePicker
            value={dateTimeVal || todayDate}
            mode="time"
            display="clock"
            onChange={(e, val) => {
              setShowTimePicker(false)

              onSelect(clampDate(minimumDate, val, maximumDate))
            }}
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
                value={dateTimeVal || todayDate}
                mode={pickTime ? "datetime" : "date"}
                display="inline"
                onChange={(e, val) => {
                  setDateTimeVal(clampDate(minimumDate, val, maximumDate))
                }}
                minimumDate={minimumDate}
                maximumDate={maximumDate}
                style={{
                  opacity: 1,
                }}
              />
              <Button
                onPress={() => {
                  onSelect(dateTimeVal)
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
            suffix={clearButton()}
          >
            {dateObject
              ? pickTime
                ? formatDate(dateObject)
                : formatDateOnly(dateObject)
              : placeholder}
          </Button>
        )}
      </>
    )
  }
}
