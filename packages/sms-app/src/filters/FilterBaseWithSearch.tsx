import React, { useEffect, useState } from "react"
import { ScrollView } from "react-native"
import { Div, Icon, Input, Button, Text } from "react-native-magnus"
import Modal from "react-native-modal"

import DropdownInput from "components/DropdownInput"

import useDebounce from "hooks/useDebounce"

import { COLOR_PRIMARY } from "helper/theme"

export type FilterBaseChildrenProps<FilterType extends Record<string, any>> = {
  setFilter: (name: keyof FilterType) => (value) => void
  values: FilterType
}

type PropTypes<FilterType extends Record<string, any>> = {
  activeFilterValues: FilterType
  onSetFilter: (newFilter: Partial<FilterType>) => void
  searchBy: keyof FilterType
  searchPlaceholder: string
  children: (props: FilterBaseChildrenProps<FilterType>) => React.ReactNode
  disableFilter?: boolean

  sortOptions?: Array<string>
  activeSort?: string
  onSetSort?: (val) => void
  ascendingSort?: boolean
  setAscendingSort?: (flag: boolean) => void
}

export default function FilterBase<FilterType extends Record<string, any>>({
  activeFilterValues,
  onSetFilter,
  searchBy,
  searchPlaceholder,
  children,
  disableFilter = false,

  sortOptions = [],
  activeSort = "",
  onSetSort = (val) => {},
  ascendingSort = true,
  setAscendingSort = (val) => {},
}: PropTypes<FilterType>) {
  const [modalVisible, setModalVisible] = useState(false)

  const [sortValue, setSortValue] = useState(activeSort)
  const [isAscending, setIsAscending] = useState(ascendingSort)

  const [filterValues, setFilterValues] =
    useState<FilterType>(activeFilterValues)
  const debouncedSearch = useDebounce(filterValues[searchBy], 500)

  useEffect(() => {
    applyFilter()
  }, [debouncedSearch])

  const applyFilter = () => {
    onSetFilter(
      Object.entries(filterValues).reduce(
        (acc, [key, val]) => (val === null ? acc : { ...acc, [key]: val }),
        {},
      ),
    )
    onSetSort(sortValue)
    setAscendingSort(isAscending)
    setModalVisible(false)
  }

  const renderFilterButton = () => {
    if (!!activeFilterValues) {
      let length = Object.keys(activeFilterValues).length
      if (
        length === 0 ||
        (length === 1 && activeFilterValues[searchBy]) ||
        (length === 1 && activeFilterValues[searchBy] === "")
      ) {
        return (
          <Button
            p={5}
            bg="white"
            rounded="circle"
            onPress={() => setModalVisible(true)}
            justifyContent="center"
            alignSelf="center"
            disabled={disableFilter}
          >
            <Icon
              name="filter"
              fontFamily="AntDesign"
              fontSize={18}
              color="primary"
            />
          </Button>
        )
      } else {
        return (
          <Button
            p={5}
            bg="primary"
            rounded="circle"
            onPress={() => setModalVisible(true)}
            alignSelf="center"
            disabled={disableFilter}
          >
            <Icon
              name="filter"
              fontFamily="AntDesign"
              fontSize={18}
              color="white"
            />
          </Button>
        )
      }
    }
  }

  return (
    <>
      <Div bg="white" shadow="sm" px={20} py={10} zIndex={5}>
        <Div row>
          <Div row flex={1} justifyContent="center">
            <Input
              flex={1}
              mr={10}
              placeholder={searchPlaceholder}
              focusBorderColor="primary"
              value={filterValues[searchBy] || ""}
              onChangeText={(val) => {
                setFilterValues({ ...filterValues, [searchBy]: val })
              }}
            />
            {renderFilterButton()}
          </Div>
        </Div>
      </Div>
      <Modal
        useNativeDriver
        isVisible={modalVisible}
        animationIn="slideInUp"
        animationOut="slideOutDown"
        onBackdropPress={() => setModalVisible(false)}
        propagateSwipe
      >
        <Div p={20} bg="white" h={700}>
          <ScrollView showsVerticalScrollIndicator={false}>
            {sortOptions.length > 0 && (
              <Div mb={20}>
                <Text fontSize={14} fontWeight="bold" mb={20}>
                  Sort By
                </Text>
                <Div flexDir="row" alignItems="center">
                  <Div flex={1}>
                    <DropdownInput
                      data={sortOptions}
                      message="Please select a sorting value"
                      title="Sort By"
                      value={sortValue}
                      onSelect={setSortValue}
                    />
                  </Div>

                  <Div ml={5}>
                    {isAscending ? (
                      // Asc Button
                      <Button
                        p={5}
                        bg="white"
                        rounded="circle"
                        onPress={() => {
                          setIsAscending(false)
                        }}
                        justifyContent="center"
                        alignSelf="center"
                        disabled={disableFilter}
                      >
                        <Icon
                          name="sort-amount-asc"
                          fontFamily="FontAwesome"
                          fontSize={18}
                          color="primary"
                        />
                      </Button>
                    ) : (
                      // Desc button
                      <Button
                        p={5}
                        bg="white"
                        rounded="circle"
                        onPress={() => {
                          setIsAscending(true)
                        }}
                        justifyContent="center"
                        alignSelf="center"
                        disabled={disableFilter}
                      >
                        <Icon
                          name="sort-amount-desc"
                          fontFamily="FontAwesome"
                          fontSize={18}
                          color="primary"
                        />
                      </Button>
                    )}
                  </Div>
                </Div>
              </Div>
            )}

            <Text fontSize={14} fontWeight="bold">
              Filter
            </Text>

            {children({
              setFilter: (name) => (value) =>
                setFilterValues({ ...filterValues, [name]: value }),
              values: filterValues,
            })}

            <Button
              onPress={applyFilter}
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
            {(Object.values({ ...filterValues, ...activeFilterValues }).some(
              (x) => x !== null,
            ) ||
              !!sortValue) && (
              <Button
                onPress={() => {
                  setFilterValues({} as any)
                  onSetFilter({})
                  setSortValue("")
                  onSetSort("")
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
          </ScrollView>
        </Div>
      </Modal>
    </>
  )
}
