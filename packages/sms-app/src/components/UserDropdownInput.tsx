/* eslint-disable @typescript-eslint/no-use-before-define */
import React, { useMemo, useState } from "react"
import { Button, Div, Input } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useDebounce from "hooks/useDebounce"
import useMultipleQueries from "hooks/useMultipleQueries"

import useUserList from "api/hooks/user/useUserList"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

import { User } from "types/User"

type PropTypes = {
  value: string
  onSelect: (value) => void
  disabled?: boolean
  multiple?: boolean
}

export default ({ value, onSelect, disabled, multiple = false }: PropTypes) => {
  const [visible, setVisible] = useState(false)
  const [filter, setFilter] = useState(null)

  const debouncedVal = useDebounce(filter, 500)
  const {
    queries: [{ data: userPaginatedData }, { data: userData }],
    meta,
  } = useMultipleQueries([
    useUserList({ sort: "name", filterName: debouncedVal || "" }),
    useUserLoggedInData(),
  ] as const)
  const { isError, isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } =
    meta

  const data: User[] = dataFromPaginated(userPaginatedData)?.filter(
    (x) => x.id !== userData.id,
  )

  const activeUser = useMemo(
    () =>
      !!data &&
      data.length > 0 &&
      data.find((x) => x.id === parseInt(value, 10)),
    [value, data],
  )

  const renderText = () => {
    if (!!multiple) {
      if (value.length > 1) {
        return "Multiple users selected"
      }
      if (value.length === 1 && !!activeUser) {
        return <Text>{activeUser.name}</Text>
      }
    }

    if (!multiple && !!activeUser) {
      return <Text>{activeUser.name}</Text>
    }

    return "Click to select a customer"
  }
  return (
    <>
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
        disabled={disabled || isLoading}
      >
        {renderText()}
      </Button>
      <Select
        multiple={multiple}
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title="User List"
        message={
          <>
            <Text mb={5}>Please select user(s)</Text>
            <Input
              mr={10}
              placeholder="Search by user name"
              focusBorderColor="primary"
              value={filter}
              onChangeText={(val) => {
                setFilter(val)
              }}
            />
          </>
        }
        data={data || []}
        onEndReached={() => {
          hasNextPage && fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `user${item.id}`}
        renderItem={(item, index) => (
          <Select.Option
            value={item?.id?.toString()}
            p={20}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            <Div row>
              <Text>{item.name}</Text>
              <Text color="grey" ml={5}>
                - {item.as}
              </Text>
            </Div>
          </Select.Option>
        )}
      />
    </>
  )
}
