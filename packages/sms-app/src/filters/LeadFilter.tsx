import React, { useEffect, useState } from "react"
import { useQuery } from "react-query"

import BooleanDropdownInput from "components/BooleanDropdownInput"
import ChannelDropdownInput from "components/ChannelDropdownInput"
import DropdownInput from "components/DropdownInput"
import LeadCategoryDropdownInput from "components/LeadCategoryDropdownInput"
import LeadSubcategoryDropdownInput from "components/LeadCategoryDropdownInput"
import SubcategoryLead from "components/SubcategoryLead"
import Text from "components/Text"
import UserForReportDropdownInput from "components/UserForReportDropdownInput"

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
      searchPlaceholder="Search by name/phone/email"
      activeFilterValues={activeFilterValues}
      sortOptions={["id", "status"]}
      activeSort={activeSort}
      onSetSort={onSetSort}
      ascendingSort={ascendingSort}
      setAscendingSort={setAscendingSort}
    >
      {({ setFilter, values }) => (
        <>
          <Text mt={20} mb={10}>
            Channel
          </Text>
          <ChannelDropdownInput
            value={
              values?.filterChannelName ?? activeFilterValues?.filterChannelName
            }
            onSelect={setFilter("filterChannelName")}
          />

          <Text mt={20} mb={10}>
            Status
          </Text>
          <DropdownInput
            data={LeadStatusList}
            title="Status"
            message="Please select a status"
            value={values?.filterStatus ?? activeFilterValues?.filterStatus}
            onSelect={setFilter("filterStatus")}
          />
          <Text mt={20} mb={10}>
            Lead Category
          </Text>
          <LeadCategoryDropdownInput
            value={
              values?.filterLeadCategoryId ??
              activeFilterValues?.filterLeadCategoryId
            }
            onSelect={setFilter("filterLeadCategoryId")}
          />
          <Text mt={20} mb={10}>
            Lead SubCategory
          </Text>
          <SubcategoryLead
            id={values?.filterLeadCategoryId}
            title="Status"
            message="Please select a status"
            value={
              values?.filterSubLeadCategoryId ??
              activeFilterValues?.filterSubLeadCategoryId
            }
            onSelect={setFilter("filterSubLeadCategoryId")}
          />
          <Text mt={20} mb={10}>
            User
          </Text>
          <UserForReportDropdownInput
            value={
              (values?.filterUserId ?? activeFilterValues?.filterUserId) || []
            }
            multiple
            onSelect={setFilter("filterUserId")}
          />

          <Text mt={20} mb={10}>
            Has Activity
          </Text>
          <BooleanDropdownInput
            value={
              values?.filterCustomerHasActivity ??
              activeFilterValues?.filterCustomerHasActivity
            }
            onSelect={setFilter("filterCustomerHasActivity")}
          />
        </>
      )}
    </FilterBaseWithSearch>
  )
}
