import React from "react"
import { Text } from "react-native-magnus"

import DatePickerInput from "components/DatePickerInput"

import { V1ApiReportIndexRequest } from "api/openapi"

import FilterBase from "./FilterBase"

export type ReportListFilterType = Writeable<
  Pick<V1ApiReportIndexRequest, "filterPeriodAfter">
> &
  Writeable<Pick<V1ApiReportIndexRequest, "filterPeriodBefore">>

type PropTypes = {
  activeFilterValues: ReportListFilterType
  onSetFilter: (newFilter: ReportListFilterType) => void
}

export default function ReportListFilter({
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
            Date After
          </Text>
          <DatePickerInput
            placeholder="Please select date"
            value={
              values.filterPeriodAfter ?? activeFilterValues?.filterPeriodAfter
            }
            onSelect={setFilter("filterPeriodAfter")}
            maximumDate={new Date()}
          />
          <Text mt={20} mb={10}>
            Date Before
          </Text>
          <DatePickerInput
            placeholder="Please select date"
            value={
              values.filterPeriodBefore ??
              activeFilterValues?.filterPeriodBefore
            }
            onSelect={setFilter("filterPeriodBefore")}
            maximumDate={new Date()}
          />
        </>
      )}
    </FilterBase>
  )
}
