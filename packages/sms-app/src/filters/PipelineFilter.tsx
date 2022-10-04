import React, { useEffect, useState } from "react"
import { Div } from "react-native-magnus"
import { useQuery } from "react-query"

import ChannelDropdownInput from "components/ChannelDropdownInput"
import CompanyDropdownInput from "components/CompanyDropdownInput"
import DatePickerInput from "components/DatePickerInput"
import MonthPickerInput from "components/MonthPickerInput"
import Text from "components/Text"
import UserSupervisedDropdownInput from "components/UserSupervisedDropdownInput"

import { V1ApiCustomerGetLeadsRequest } from "api/openapi"

import FilterBaseWithSearch from "./FilterBaseWithSearch"

export type LeadFilterType = Writeable<
  Pick<
    V1ApiCustomerGetLeadsRequest,
    | "filterCustomerSearch"
    | "filterChannelName"
    | "filterStatus"
    | "filterLeadCategoryId"
    | "filterSubLeadCategoryId"
    | "filterCustomerHasActivity"
    | "filterUserId"
  >
> & { filterUserId?: number[] }

type PropTypes = {
  activeFilterValues: LeadFilterType
  onSetFilter: (newFilter: LeadFilterType) => void
  activeSort?: string
  onSetSort?: (val) => void
  ascendingSort?: boolean
  setAscendingSort?: (flag: boolean) => void
}

export default function LeadFilter({
  activeFilterValues,
  onSetFilter,
  activeSort = "",
  onSetSort = (val) => {},
  ascendingSort = true,
  setAscendingSort = (val) => {},
}: PropTypes) {
  return (
    <FilterBaseWithSearch
      onSetFilter={onSetFilter}
      searchBy="filterCustomerSearch"
      searchPlaceholder="Search by sales name"
      activeFilterValues={activeFilterValues}
      sortOptions={["All", "BUM", "Channel", "Sales"]}
      activeSort={activeSort}
      onSetSort={onSetSort}
      ascendingSort={ascendingSort}
      setAscendingSort={setAscendingSort}
    >
      {({ setFilter, values }) => (
        <>
          <Text mt={20} mb={10}>
            Date range
          </Text>
          <DatePickerInput
            placeholder="Select date before"
            value={values.filterUserId ?? activeFilterValues?.filterUserId}
            onSelect={setFilter("filterUserId")}
          />
          <Div h={10} />
          <DatePickerInput
            placeholder="Select date after"
            value={
              values.filterCustomerHasActivity ??
              activeFilterValues?.filterCustomerHasActivity
            }
            onSelect={setFilter("filterCustomerHasActivity")}
            minimumDate={values.filterUserId}
            maximumDate={new Date()}
          />
          <Text mt={20} mb={10}>
            Company
          </Text>
          <CompanyDropdownInput
            value={values?.filterStatus ?? activeFilterValues?.filterStatus}
            onSelect={setFilter("filterStatus")}
          />
          <Text mt={20} mb={10}>
            Channel
          </Text>
          <ChannelDropdownInput
            type="ids"
            value={
              values?.filterChannelName ?? activeFilterValues?.filterChannelName
            }
            onSelect={setFilter("filterChannelName")}
          />

          {/* <Text mt={20} mb={10}>
            BUM
          </Text>
          <UserSupervisedDropdownInput
            value={values?.filterSubLeadCategoryId}
            onSelect={setFilter("filterSubLeadCategoryId")}
          /> */}
        </>
      )}
    </FilterBaseWithSearch>
  )
}
