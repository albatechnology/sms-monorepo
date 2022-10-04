import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { ProductTagApiProductTagIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import {
  ProductTag,
  mapProductTag,
} from "types/POS/ProductCategorization/ProductTag"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: ProductTagApiProductTagIndexRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductTag[]>>(
    ["productTagList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productTagIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductTag[] = res.data.data.map(mapProductTag)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
