import React, { useMemo, useState } from "react"
import { Button, Input } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useDebounce from "hooks/useDebounce"
import useMultipleQueries from "hooks/useMultipleQueries"

import useCustomerById from "api/hooks/customer/useCustomerById"
import useCustomerList from "api/hooks/customer/useCustomerList"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

import { Customer, getFullName } from "types/Customer"

import Loading from "./Loading"

type PropTypes = {
  value: number
  onSelect: (value) => void
  disabled?: boolean
  searchOnly?: boolean
}

export default ({
  value,
  onSelect,
  disabled,
  searchOnly = false,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const [filter, setFilter] = useState(null)

  const debouncedVal = useDebounce(filter, 500)

  const enableQuery = searchOnly ? !!debouncedVal : true

  const {
    queries: [{ data: selectedCustomer }, { data: customerPaginatedData }],
    meta,
  } = useMultipleQueries(
    [
      useCustomerById(value, { enabled: !!value }),
      useCustomerList(
        {
          filterSearch: debouncedVal || "",
          sort: "first_name",
        },
        { enabled: enableQuery },
      ),
    ] as const,
    { useStandardIsLoadingBehaviour: true },
  )

  const { isError, isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } =
    meta

  const data: Customer[] = dataFromPaginated(customerPaginatedData)

  const activeCustomer = useMemo(
    () => !!selectedCustomer && selectedCustomer,
    [selectedCustomer],
  )

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
        {!!activeCustomer ? (
          <Text>{getFullName(activeCustomer)}</Text>
        ) : (
          "Click to select a customer"
        )}
      </Button>
      <Select
        isLoading={isLoading}
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title="Customer List"
        message={
          <>
            <Text mb={5}>Search customer:</Text>
            <Input
              mr={10}
              placeholder="Search by name/phone/email"
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
        ListEmptyComponent={
          <>
            {isLoading ? (
              <Loading />
            ) : enableQuery ? (
              <Text fontSize={14} textAlign="center" p={20}>
                Kosong
              </Text>
            ) : (
              <Text
                color="grey"
                fontSize={14}
                textAlign="center"
                px={20}
                pt={30}
              >
                Start searching by typing {`\n`} name/phone/email...
              </Text>
            )}
          </>
        }
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `customer${item.id}`}
        renderItem={(item, index) => (
          <Select.Option
            value={item.id.toString()}
            py={20}
            pb={10}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            {renderCustomer(item)}
          </Select.Option>
        )}
      />
    </>
  )
}

const renderCustomer = (customer: Customer) => {
  return (
    <>
      <Text mb={5}>{getFullName(customer)}</Text>
      <Text mb={5}>{customer.email}</Text>
      <Text mb={5}>{customer.phone}</Text>
    </>
  )
}
