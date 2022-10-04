import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiPromoIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Promo, mapPromo } from "types/Promo"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiPromoIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Promo[]>>(
    ["promoList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .promoIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Promo[] = res.data.data.map(mapPromo)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
