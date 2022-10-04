import React, { useEffect, useState } from "react"
import { useQuery } from "react-query"

import BooleanDropdownInput from "components/BooleanDropdownInput"
import ChannelDropdownInput from "components/ChannelDropdownInput"
import DatePickerInput from "components/DatePickerInput"
import DropdownInput from "components/DropdownInput"
import LeadCategoryDropdownInput from "components/LeadCategoryDropdownInput"
import LeadSubcategoryDropdownInput from "components/LeadCategoryDropdownInput"
import MonthPickerInput from "components/MonthPickerInput"
import SubcategoryLead from "components/SubcategoryLead"
import Text from "components/Text"
import UserForReportDropdownInput from "components/UserForReportDropdownInput"
import UserSupervisedDropdownInput from "components/UserSupervisedDropdownInput"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { LeadStatusList } from "api/generated/enums"
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
      searchPlaceholder="Search by name / brand"
      activeFilterValues={activeFilterValues}
      sortOptions={["Lead", "Brand"]}
      activeSort={activeSort}
      onSetSort={onSetSort}
      ascendingSort={ascendingSort}
      setAscendingSort={setAscendingSort}
    >
      {({ setFilter, values }) => (
        <>
          <Text mt={20} mb={10}>
            Date
          </Text>
          <MonthPickerInput
            placeholder="Filter Month"
            value={values.filterUserId ?? activeFilterValues?.filterUserId}
            onSelect={setFilter("filterUserId")}
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

          <Text mt={20} mb={10}>
            BUM
          </Text>
          <UserSupervisedDropdownInput
            value={values?.filterSubLeadCategoryId}
            onSelect={setFilter("filterSubLeadCategoryId")}
          />
        </>
      )}
    </FilterBaseWithSearch>
  )
}
