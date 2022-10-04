import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiProductVersionRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import {
  ProductVersion,
  mapProductVersion,
} from "types/POS/Product/ProductVersion"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: V1ApiProductVersionRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductVersion[]>>(
    ["productVersionList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productVersion({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductVersion[] = res.data.data.map(mapProductVersion)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
