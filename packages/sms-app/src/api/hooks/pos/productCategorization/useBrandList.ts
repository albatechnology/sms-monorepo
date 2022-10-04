import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiProductBrandRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Brand, mapBrand } from "types/POS/ProductCategorization/Brand"

import standardErrorHandling from "../../../errors"

export default (requestObject?: V1ApiProductBrandRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Brand[]>>(
    ["brandList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productBrand({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Brand[] = res.data.data.map(mapBrand)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
