import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiPromoCategoryIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Promo, mapPromo } from "types/Promo"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiPromoCategoryIndexRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Promo[]>>(
    ["promo", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .promoCategoryIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Promo[] = res.data.data.map(mapPromo)
          return { ...res.data, data: res.data.data }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
