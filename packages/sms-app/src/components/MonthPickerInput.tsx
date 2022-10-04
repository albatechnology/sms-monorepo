import { format } from "date-fns"
import React, { useState } from "react"
import { Button, Div, Icon } from "react-native-magnus"
import Modal from "react-native-modal"
import MonthPicker from "react-native-month-picker"

type PropTypes = {
  placeholder: string
  value: Date
  onSelect: (value: Date) => void
  disabled?: boolean
  minimumDate?: Date
  maximumDate?: Date
}

export default ({
  placeholder = "",
  value,
  onSelect,
  disabled = false,
  minimumDate = new Date("1990-01-01"),
  maximumDate = new Date(),
}: PropTypes) => {
  const [showMonthPicker, setShowMonthPicker] = useState(false)

  const todayDate = new Date()

  const dateObject = !!value ? new Date(value) : null

  const onChange = (newDate) => {
    setShowMonthPicker(false)
    onSelect(new Date(newDate))
  }

  const clearButton = () => {
    if (dateObject) {
      return (
        <Button bg="white" p={10} my={-10} onPress={() => onSelect(null)}>
          <Icon name="close" color="grey" fontSize={15} />
        </Button>
      )
    }
  }

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
        onPress={() => setShowMonthPicker(true)}
        disabled={disabled}
        suffix={clearButton()}
      >
        {dateObject ? format(dateObject, "MMM yyyy") : placeholder}
      </Button>
      <Modal
        useNativeDriver
        isVisible={showMonthPicker}
        animationIn="slideInUp"
        animationOut="slideOutDown"
        onBackdropPress={() => setShowMonthPicker(false)}
      >
        <Div>
          <Div>
            <MonthPicker
              maxDate={maximumDate}
              minDate={minimumDate}
              selectedDate={dateObject || todayDate}
              onMonthChange={onChange}
            />
          </Div>
        </Div>
      </Modal>
    </>
  )
}
