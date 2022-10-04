import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { ProductUnitApiProductUnitIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { ProductUnit, mapProductUnit } from "types/POS/ProductUnit/ProductUnit"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: ProductUnitApiProductUnitIndexRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductUnit[]>>(
    ["productUnitList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productUnitIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductUnit[] = res.data.data.map(mapProductUnit)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
