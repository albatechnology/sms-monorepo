import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiStockIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Stock, mapStock } from "types/Stock"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiStockIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Stock[]>>(
    ["stockList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .stockIndexExtended({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Stock[] = res.data.data.map(mapStock)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
