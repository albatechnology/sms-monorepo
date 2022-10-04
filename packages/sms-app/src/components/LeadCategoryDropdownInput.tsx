import React, { useMemo, useState } from "react"
import { Button } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useLeadCategoryList from "api/hooks/leadCategory/useLeadCategoryList"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

import { LeadCategory } from "types/LeadCategory"

type PropTypes = {
  value: string | any[]
  onSelect: (value) => void
  onPress: (value) => void
  disabled?: boolean
  multiple?: boolean
}

export default ({
  value,
  onSelect,
  onPress,
  disabled,
  multiple = false,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const {
    queries: [{ data: leadCategoryPaginatedData }],
    meta,
  } = useMultipleQueries([useLeadCategoryList()] as const)

  const { isError, isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } =
    meta
  const data: LeadCategory[] = dataFromPaginated(leadCategoryPaginatedData)

  const activeLeadCategory = useMemo(
    () =>
      !!data &&
      data.length > 0 &&
      data.find((x) => x.id === parseInt(value as string, 10)),
    [value, data],
  )
  const renderText = () => {
    if (!!multiple) {
      if (value.length > 1) {
        return "Multiple lead category selected"
      }
      if (value.length === 1 && !!activeLeadCategory) {
        return <Text>{activeLeadCategory?.name}</Text>
      }
    }

    if (!multiple && !!activeLeadCategory) {
      return <Text>{activeLeadCategory?.name}</Text>
    }

    return "Click to select lead category"
  }

  return (
    <>
      <Button
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
        title="Lead category List"
        message="Please select lead category"
        data={data || []}
        onEndReached={() => {
          hasNextPage && fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `lead_category_${item.id}`}
        renderItem={(item, index) => (
          <Select.Option
            value={item?.id?.toString()}
            p={20}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
            onSelect={onPress}
          >
            {item.name}
          </Select.Option>
        )}
      />
    </>
  )
}
