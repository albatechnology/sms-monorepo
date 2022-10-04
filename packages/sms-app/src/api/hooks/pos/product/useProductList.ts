import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { ProductApiProductIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Product, mapProduct } from "types/POS/Product/Product"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: ProductApiProductIndexRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Product[]>>(
    ["productList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Product[] = res.data.data.map(mapProduct)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
