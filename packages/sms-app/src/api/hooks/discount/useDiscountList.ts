import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiDiscountIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Discount, mapDiscount } from "types/Discount"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiDiscountIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Discount[]>>(
    ["discountList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .discountIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Discount[] = res.data.data.map(mapDiscount)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
