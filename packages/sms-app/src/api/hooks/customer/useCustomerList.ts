import { UseInfiniteQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiCustomerIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Customer, mapCustomer } from "types/Customer"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiCustomerIndexRequest,
  extraProps?: UseInfiniteQueryOptions<any, any, any>,
  perPage = 20,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Customer[]>, string>(
    ["customerList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .customerIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Customer[] = res.data.data.map(mapCustomer)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )
  return queryData
}
