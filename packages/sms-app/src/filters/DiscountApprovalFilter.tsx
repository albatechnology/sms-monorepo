import React from "react"
import { Button, Dropdown, Input, Text } from "react-native-magnus"

import { V1ApiOrderListApprovalRequest } from "api/openapi"

import FilterBase from "./FilterBase"

export type DiscountApprovalFilterType = Writeable<
  Pick<V1ApiOrderListApprovalRequest, "filterInvoiceNumber">
> &
  Writeable<Pick<V1ApiOrderListApprovalRequest, "filterApprovalStatus">>

type PropTypes = {
  activeFilterValues: DiscountApprovalFilterType
  onSetFilter: (newFilter: DiscountApprovalFilterType) => void
}
const dropdownRef = React.createRef()

export default function DiscountApprovalFilter({
  activeFilterValues,
  onSetFilter,
}: PropTypes) {
  return (
    <FilterBase
      onSetFilter={onSetFilter}
      activeFilterValues={activeFilterValues}
    >
      {({ setFilter, values }) => (
        <>
          <Text mt={20} mb={10}>
            Invoice Number
          </Text>
          <Input
            mr={10}
            placeholder="Search by invoice number"
            focusBorderColor="primary"
            value={
              values.filterInvoiceNumber ??
              activeFilterValues?.filterInvoiceNumber
            }
            onChangeText={setFilter("filterInvoiceNumber")}
          />
          <Button
            block
            bg="pink500"
            mt="sm"
            p="md"
            color="white"
            onPress={() => dropdownRef.current.open()}
          >
            Open Dropdown
          </Button>
          <Dropdown
            ref={dropdownRef}
            title={
              <Text mx="xl" color="gray500" pb="md">
                This is your title
              </Text>
            }
            mt="md"
            pb="2xl"
            showSwipeIndicator={true}
            roundedTop="xl"
          >
            <Dropdown.Option
              value={values.filterApprovalStatus}
              onPress={setFilter("filterApprovalStatus")}
              py="md"
              px="xl"
              block
            >
              First Option
            </Dropdown.Option>
          </Dropdown>
        </>
      )}
    </FilterBase>
  )
}
