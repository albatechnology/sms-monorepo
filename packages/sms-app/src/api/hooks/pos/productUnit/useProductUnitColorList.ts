import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiProductUnitColoursRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import {
  ProductUnitColor,
  mapProductUnitColor,
} from "types/POS/ProductUnit/ProductUnitColor"

import standardErrorHandling from "../../../errors"

export default (
  requestObject?: V1ApiProductUnitColoursRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ProductUnitColor[]>>(
    ["productUnitColorList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .productUnitColours({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: ProductUnitColor[] =
            res.data.data.map(mapProductUnitColor)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
