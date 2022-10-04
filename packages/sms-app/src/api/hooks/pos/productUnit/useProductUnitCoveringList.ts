import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiProductUnitCoveringsRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import {
  ProductUnitCovering,
  mapProductUnitCovering,
} from "types/POS/ProductUnit/ProductUnitCovering"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: V1ApiProductUnitCoveringsRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductUnitCovering[]>>(
    ["productUnitCoveringList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productUnitCoverings({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductUnitCovering[] = res.data.data.map(
            mapProductUnitCovering,
          )
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
