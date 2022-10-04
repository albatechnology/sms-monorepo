import React, { useMemo, useState } from "react"
import { TouchableOpacity } from "react-native"
import { Button, Input, Icon } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useDebounce from "hooks/useDebounce"
import useMultipleQueries from "hooks/useMultipleQueries"

import useChannelList from "api/hooks/channel/useChannelList"
import { V1ApiChannelIndexRequest } from "api/openapi"

import { responsive } from "helper"
import { dataFromPaginated } from "helper/pagination"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

import { Channel } from "types/Channel"

type PropTypes = {
  value: string | any[]
  onSelect: (value) => void
  disabled?: boolean
  multiple?: boolean
  type?: string
  extraQueryRequestObject?: V1ApiChannelIndexRequest
  flex?: boolean
}

export default ({
  value,
  onSelect,
  disabled,
  multiple = false,
  extraQueryRequestObject,
  flex = false,
  type,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const [filter, setFilter] = useState(null)

  const debouncedVal = useDebounce(filter, 500)

  const {
    queries: [{ data: channelPaginatedData }],
    meta,
  } = useMultipleQueries([
    useChannelList({
      ...extraQueryRequestObject,
      filterName: debouncedVal || "",
    }),
  ] as const)

  const { isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } = meta

  const data: Channel[] = dataFromPaginated(channelPaginatedData)

  const activeChannel = useMemo(
    () =>
      !!data &&
      data.length > 0 &&
      data.find((x) => x.id === parseInt(value as string, 10)),
    [value, data],
  )

  const renderText = () => {
    if (!!multiple) {
      if (value.length > 1) {
        return "Multiple channels selected"
      }
      if (value.length === 1 && !!activeChannel) {
        return <Text>{activeChannel.name}</Text>
      }
    }

    if (!multiple && !!activeChannel) {
      return <Text>{activeChannel.name}</Text>
    }

    return "Click to select channel(s)"
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
    if (activeChannel) {
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
        flex={flex ? 1 : 0}
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
        title="Channel List"
        message={
          <>
            <Text mb={5}>Please select channel(s)</Text>
            <Input
              mr={10}
              placeholder="Search by channel name"
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
        keyExtractor={(item, index) => `channel_${item.id}`}
        renderItem={(item, index) => (
          <Select.Option
            value={type === "ids" ? item?.id : item?.name?.toString()}
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
