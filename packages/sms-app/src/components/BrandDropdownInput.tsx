/* eslint-disable @typescript-eslint/no-unused-expressions */
import React, { useMemo, useState } from "react"
import { Button, Div, Input } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useBrandList from "api/hooks/pos/productCategorization/useBrandList"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

import { Brand } from "types/POS/ProductCategorization/Brand"

type PropTypes = {
  value: string
  onSelect: (value) => void
  disabled?: boolean
  multiple?: boolean
}

export default ({ value, onSelect, disabled, multiple = false }: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const {
    queries: [{ data: brandPaginatedData }],
    meta,
  } = useMultipleQueries([useBrandList()] as const)

  const { isError, isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } =
    meta

  const data: Brand[] = dataFromPaginated(brandPaginatedData)

  const activeBrand = useMemo(
    () =>
      !!data &&
      data.length > 0 &&
      data.find((x) => x.id === parseInt(value, 10)),
    [value, data],
  )

  const renderText = () => {
    if (!!multiple) {
      if (value.length > 1) {
        return "Multiple brands selected"
      }
      if (value.length === 1 && !!activeBrand) {
        return <Text>{activeBrand.name}</Text>
      }
    }

    if (!multiple && !!activeBrand) {
      return <Text>{activeBrand.name}</Text>
    }

    return "Click to (s)"
  }
  const [checked, setChecked] = useState([])
  return (
    <>
      <Button
        block
        borderWidth={1}
        bg="white"
        color={
          (!Array.isArray(value) && value) ||
          (Array.isArray(value) && value.length > 0)
            ? "primary"
            : COLOR_PLACEHOLDER
        }
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
        title="Brand List"
        message="Please select brand(s)"
        data={data || []}
        onEndReached={() => {
          hasNextPage && fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `brand_${item.id}`}
        renderItem={(item, index) => {
          return (
            <Select.Option
              value={item?.id?.toString()}
              p={20}
              borderBottomWidth={0.8}
              borderBottomColor={COLOR_DISABLED}
            >
              {item.name}
            </Select.Option>
          )
        }}
      />
    </>
  )
}
