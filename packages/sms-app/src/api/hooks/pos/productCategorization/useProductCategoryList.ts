import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { ProductCategoryApiProductCategoryIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import {
  ProductCategory,
  mapProductCategory,
} from "types/POS/ProductCategorization/ProductCategory"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: ProductCategoryApiProductCategoryIndexRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductCategory[]>>(
    ["productCategoryList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productCategoryIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductCategory[] = res.data.data.map(mapProductCategory)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
