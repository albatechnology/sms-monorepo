import React from "react"

import { V1ApiProductModelRequest } from "api/openapi"

import FilterBaseWithSearch from "./FilterBaseWithSearch"

export type ModelFilterType = Writeable<
  Pick<V1ApiProductModelRequest, "filterName" | "filterProductBrandId">
>

type PropTypes = {
  activeFilterValues: ModelFilterType
  onSetFilter: (newFilter: ModelFilterType) => void
}

export default function LeadFilter({
  activeFilterValues,
  onSetFilter,
}: PropTypes) {
  return (
    <FilterBaseWithSearch
      onSetFilter={onSetFilter}
      searchBy="filterName"
      searchPlaceholder="Search by product unit name"
      activeFilterValues={activeFilterValues}
      disableFilter={true}
    >
      {({ setFilter, values }) => <></>}
    </FilterBaseWithSearch>
  )
}
