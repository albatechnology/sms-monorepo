import React from "react"

import BrandDropdownInput from "components/BrandDropdownInput"
import Text from "components/Text"

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
      searchPlaceholder="Search by model name"
      activeFilterValues={activeFilterValues}
    >
      {({ setFilter, values }) => (
        <>
          <Text mt={20} mb={10}>
            Brand
          </Text>
          <BrandDropdownInput
            value={
              values?.filterProductBrandId ??
              activeFilterValues?.filterProductBrandId
            }
            onSelect={setFilter("filterProductBrandId")}
          />
        </>
      )}
    </FilterBaseWithSearch>
  )
}
