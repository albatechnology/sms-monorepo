import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiProductCategoryCodesRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import {
  ProductCategoryCode,
  mapProductCategoryCode,
} from "types/POS/Product/ProductCategoryCode"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: V1ApiProductCategoryCodesRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductCategoryCode[]>>(
    ["productCategoryCodeList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productCategoryCodes({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductCategoryCode[] = res.data.data.map(
            mapProductCategoryCode,
          )
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
