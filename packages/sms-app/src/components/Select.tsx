import React, { useState, useEffect } from "react"
import { FlatList, Platform } from "react-native"
import { Button, Div, Icon, Modal } from "react-native-magnus"

import Text from "components/Text"

import { COLOR_PRIMARY } from "helper/theme"

import Loading from "./Loading"

const Select = ({
  visible,
  setVisible,
  bg = "white",
  isLoading = false,
  title = null,
  message = null,
  multiple = false,
  data = [],
  value,
  onSelect: onSelectProp,
  renderItem,
  keyExtractor = (_, index) => `${index}`,
  onEndReached = () => {},
  ListFooterComponent = null,
  ListEmptyComponent = null,
}) => {
  const [selectedValue, setSelectedValue] = useState(value)

  useEffect(() => {
    setSelectedValue(value)
  }, [value])

  const onHide = () => setVisible(false)

  const onSelect = (value: any) => {
    let finalValue

    if (multiple) {
      const copy = selectedValue.slice()
      const index = selectedValue.indexOf(value)
      if (index !== -1) {
        copy.splice(index, 1)
      } else {
        copy.push(value)
      }

      setSelectedValue(copy)
      finalValue = copy
    } else {
      setSelectedValue(value)
      setVisible(false)
      finalValue = value
    }

    onSelectProp(finalValue)
  }

  const renderFooter = () => {
    if (!multiple) {
      return
    } else {
      return (
        <Div p={20} bg="white" shadow={Platform.OS === "android" ? "md" : 0}>
          <Button
            block
            onPress={onHide}
            bg="primary"
            py={10}
            alignSelf="center"
          >
            <Text fontWeight="bold" color="white">
              Done
            </Text>
          </Button>
        </Div>
      )
    }
  }

  return (
    <Modal
      useNativeDriver
      isVisible={visible}
      onBackdropPress={onHide}
      onDismiss={onHide}
      onBackButtonPress={onHide}
      animationIn={"slideInUp"}
      h="70%"
      roundedTop={30}
    >
      <Div flex={1} bg={bg} pt={20} rounded={30}>
        <Div px={20} pt={20} pb={10}>
          {!!title &&
            (typeof title === "string" ? (
              <Text fontSize={14} fontWeight="bold" mb={10}>
                {title}
              </Text>
            ) : (
              title
            ))}
          {!!message && typeof message === "string" ? (
            <Text mb={10}>{message}</Text>
          ) : (
            message
          )}
        </Div>
        <FlatList
          data={data}
          keyExtractor={keyExtractor}
          showsVerticalScrollIndicator={false}
          onEndReachedThreshold={0.2}
          onEndReached={onEndReached}
          ListFooterComponent={ListFooterComponent}
          ListEmptyComponent={
            ListEmptyComponent ? (
              ListEmptyComponent
            ) : (
              <>
                {isLoading ? (
                  <Loading />
                ) : (
                  <Text fontSize={14} textAlign="center" p={20}>
                    Kosong
                  </Text>
                )}
              </>
            )
          }
          renderItem={({ item, index }) =>
            React.cloneElement(renderItem(item, index), {
              onSelect,
              selectedValue,
            })
          }
        />
        {renderFooter()}
      </Div>
    </Modal>
  )
}

const SelectOption = ({
  children,
  value,
  onSelect = (value) => {},

  selectedValue = "",
  ...rest
}) => {
  const isSelected = Array.isArray(selectedValue)
    ? selectedValue.includes(value)
    : selectedValue === value

  const onPress = () => {
    if (onSelect) {
      onSelect(value)
    }
  }

  const renderPrefix = () => {
    if (isSelected) {
      return (
        <Icon
          name="ios-checkmark-circle"
          fontFamily="Ionicons"
          fontSize={16}
          color={COLOR_PRIMARY}
          mr={10}
        />
      )
    }
    return null
  }

  const renderChildren = () => {
    if (typeof children === "string") {
      return <Text>{children}</Text>
    }

    return children
  }

  return (
    <Button
      {...rest}
      bg="white"
      px={20}
      onPress={onPress}
      block
      alignItems="center"
    >
      <Div minW="8%">{renderPrefix()}</Div>
      <Div flex={1} bg="transparent">
        {renderChildren()}
      </Div>
    </Button>
  )
}

Select.Option = SelectOption

export { Select }
