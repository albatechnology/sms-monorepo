import React, { useMemo, useState } from "react"
import { Button } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useCompanyList from "api/hooks/company/useCompanyList"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

import { Company } from "types/Company"

type PropTypes = {
  value: string | any[]
  onSelect: (value) => void
  disabled?: boolean
  multiple?: boolean
}

export default ({ value, onSelect, disabled, multiple = false }: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const {
    queries: [{ data: companyPaginatedData }],
    meta,
  } = useMultipleQueries([useCompanyList()] as const)

  const { isError, isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } =
    meta

  const data: Company[] = dataFromPaginated(companyPaginatedData)

  const activeCompany = useMemo(
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
      if (value.length === 1 && !!activeCompany) {
        return <Text>{activeCompany.name}</Text>
      }
    }

    if (!multiple && !!activeCompany) {
      return <Text>{activeCompany.name}</Text>
    }

    return "Click to select company(s)"
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
        title="Company List"
        message="Please select company(s)"
        data={data || []}
        onEndReached={() => {
          hasNextPage && fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `company_${item.id}`}
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
