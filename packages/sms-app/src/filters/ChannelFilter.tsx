import React from "react"

import { V1ApiChannelIndexRequest } from "api/openapi"

import FilterBaseWithSearch from "./FilterBaseWithSearch"

export type ChannelFilterType = Writeable<
  Pick<V1ApiChannelIndexRequest, "filterName">
>

type PropTypes = {
  activeFilterValues: ChannelFilterType
  onSetFilter: (newFilter: ChannelFilterType) => void
}

export default function ChannelFilter({
  activeFilterValues,
  onSetFilter,
}: PropTypes) {
  return (
    <FilterBaseWithSearch
      onSetFilter={onSetFilter}
      searchBy="filterName"
      searchPlaceholder="Search by name"
      activeFilterValues={activeFilterValues}
      disableFilter={true}
    >
      {({ setFilter, values }) => <></>}
    </FilterBaseWithSearch>
  )
}
