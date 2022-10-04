import React, { useMemo, useState } from "react"
import { TouchableOpacity } from "react-native"
import { Button, Icon, Input } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useDebounce from "hooks/useDebounce"
import useMultipleQueries from "hooks/useMultipleQueries"

import useUserForReportList from "api/hooks/user/useUserForReportList"
import { V1ApiUserListForReportRequest } from "api/openapi"

import { responsive } from "helper"
import { dataFromPaginated } from "helper/pagination"
import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

import { User } from "types/User"

type PropTypes = {
  value: string | any[]
  onSelect: (value) => void
  disabled?: boolean
  multiple?: boolean
  extraQueryRequestObject?: V1ApiUserListForReportRequest
  filterChannelIds?: Array<number>
}

export default ({
  value,
  onSelect,
  disabled,
  multiple = false,
  extraQueryRequestObject,
  filterChannelIds,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const [filter, setFilter] = useState(null)

  const debouncedVal = useDebounce(filter, 500)

  const {
    queries: [{ data: userPaginatedData }],
    meta,
  } = useMultipleQueries([
    useUserForReportList({
      sort: "name",
      ...extraQueryRequestObject,
      filterName: debouncedVal || "",
    }),
  ] as const)

  const { isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } = meta

  const rawData: User[] = dataFromPaginated(userPaginatedData)

  const data =
    !!rawData && !!filterChannelIds
      ? rawData.filter((x) => filterChannelIds.includes(x.channelId))
      : rawData

  const activeUser = useMemo(
    () =>
      !!data &&
      data.length > 0 &&
      data.find((x) => x.id === parseInt(value as string, 10)),
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

    return "Click to select user"
  }

  const renderClearButton = () => {
    if (!!filter) {
      return (
        <TouchableOpacity onPress={() => setFilter(null)}>
          <Icon
            pl={5}
            fontSize={responsive(14)}
            name="close"
            color="primary"
            fontFamily="Ionicons"
          />
        </TouchableOpacity>
      )
    }
  }

  const clearButton = () => {
    if (activeUser) {
      return (
        <Button
          ml="auto"
          bg="white"
          p={10}
          my={-10}
          onPress={() => onSelect([])}
        >
          <Icon name="close" color="grey" fontSize={15} />
        </Button>
      )
    }
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
        suffix={clearButton()}
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
              placeholder="Search by name"
              focusBorderColor="primary"
              value={filter}
              onChangeText={(val) => {
                setFilter(val)
              }}
              suffix={renderClearButton()}
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
            {item.name}
          </Select.Option>
        )}
      />
    </>
  )
}
