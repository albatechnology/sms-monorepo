import React, { useState } from "react"
import { Pressable } from "react-native"
import { Div, Icon, Button, Text } from "react-native-magnus"
import Modal from "react-native-modal"

import { COLOR_PRIMARY, COLOR_RED } from "helper/theme"

export type FilterBaseChildrenProps<FilterType extends Record<string, any>> = {
  setFilter: (name: keyof FilterType) => (value) => void
  values: FilterType
}

type PropTypes<FilterType extends Record<string, any>> = {
  activeFilterValues: FilterType
  onSetFilter: (newFilter: Partial<FilterType>) => void
  onClearFilter?: () => void
  children: (props: FilterBaseChildrenProps<FilterType>) => React.ReactNode
}

export default function FilterBase<FilterType extends Record<string, any>>({
  activeFilterValues,
  onSetFilter,
  onClearFilter = () => {},
  children,
}: PropTypes<FilterType>) {
  const [modalVisible, setModalVisible] = useState(false)

  const [filterValues, setFilterValues] =
    useState<FilterType>(activeFilterValues)
  return (
    <>
      <Div bg="white" shadow="sm" p={20} zIndex={5}>
        <Pressable onPress={() => setModalVisible(true)}>
          <Div row>
            <Div row flex={1} justifyContent="center">
              <Icon
                name="filter"
                fontFamily="AntDesign"
                fontSize={16}
                color={COLOR_PRIMARY}
                mr={5}
              />

              <Text fontSize={14} fontWeight="bold">
                Filter
              </Text>
            </Div>
            {!!activeFilterValues &&
              Object.keys(activeFilterValues).length > 0 && (
                <Pressable
                  onPress={() => {
                    setFilterValues({} as any)
                    onSetFilter({})
                    onClearFilter()
                  }}
                >
                  <Text fontSize={14} color={COLOR_RED}>
                    Clear
                  </Text>
                </Pressable>
              )}
          </Div>
        </Pressable>
      </Div>
      <Modal
        useNativeDriver
        isVisible={modalVisible}
        animationIn="slideInUp"
        animationOut="slideOutDown"
        onBackdropPress={() => setModalVisible(false)}
      >
        <Div p={20} bg="white">
          <Text fontSize={14} fontWeight="bold">
            Filter
          </Text>

          {children({
            setFilter: (name) => (value) =>
              setFilterValues({ ...filterValues, [name]: value }),
            values: filterValues,
          })}

          <Button
            onPress={() => {
              onSetFilter(
                Object.entries(filterValues).reduce(
                  (acc, [key, val]) =>
                    val === null ? acc : { ...acc, [key]: val },
                  {},
                ),
              )
              setModalVisible(false)
            }}
            bg="primary"
            mt={30}
            mb={10}
            px={20}
            alignSelf="center"
            w={"100%"}
          >
            <Text fontWeight="bold" color="white">
              Apply Filter
            </Text>
          </Button>
          {Object.values({ ...filterValues, ...activeFilterValues }).some(
            (x) => x !== null,
          ) && (
            <Button
              onPress={() => {
                setFilterValues({} as any)
                onSetFilter({})
                onClearFilter()
                setModalVisible(false)
              }}
              bg="white"
              borderColor={COLOR_PRIMARY}
              borderWidth={0.8}
              alignSelf="center"
              w={"100%"}
            >
              <Text fontWeight="bold" color={COLOR_PRIMARY}>
                Clear filter
              </Text>
            </Button>
          )}
        </Div>
      </Modal>
    </>
  )
}
