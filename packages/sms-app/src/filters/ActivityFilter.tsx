import React from "react"
import { Text } from "react-native-magnus"

import DropdownInput from "components/DropdownInput"

import { ActivityStatusList } from "api/generated/enums"
import { V1ApiCustomerGetActivitiesRequest } from "api/openapi"

import { TimeInvervalList } from "types/TimeInterval"

import FilterBase from "./FilterBase"

export type ActivityFilterType = Writeable<
  Pick<V1ApiCustomerGetActivitiesRequest, "filterStatus">
> &
  Writeable<
    Pick<V1ApiCustomerGetActivitiesRequest, "filterFollowUpDatetimeAfter">
  >

type PropTypes = {
  activeFilterValues: ActivityFilterType
  onSetFilter: (newFilter: ActivityFilterType) => void
}

export default function ActivityFilter({
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
            Time Interval
          </Text>
          <DropdownInput
            data={TimeInvervalList}
            title="Time Interval"
            message="Please select a time interval"
            value={
              values.filterFollowUpDatetimeAfter ??
              activeFilterValues?.filterFollowUpDatetimeAfter
            }
            onSelect={setFilter("filterFollowUpDatetimeAfter")}
          />

          <Text mt={20} mb={10}>
            Status
          </Text>
          <DropdownInput
            data={ActivityStatusList}
            title="Status"
            message="Please select a status"
            value={values?.filterStatus ?? activeFilterValues?.filterStatus}
            onSelect={setFilter("filterStatus")}
          />
        </>
      )}
    </FilterBase>
  )
}
