import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiProductModelRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { ProductModel, mapProductModel } from "types/POS/Product/ProductModel"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: V1ApiProductModelRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductModel[]>>(
    ["productModelList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productModel({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductModel[] = res.data.data.map(mapProductModel)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
