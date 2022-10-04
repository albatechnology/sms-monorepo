import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiAddressIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Address, mapAddress } from "types/Address"

import standardErrorHandling from "../../errors"

export default (
  customerId: number,
  requestObject?: V1ApiAddressIndexRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Address[]>>(
    ["addressList", customerId, requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .addressIndex({
          filterCustomerId: customerId,
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Address[] = res.data.data.map(mapAddress)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
