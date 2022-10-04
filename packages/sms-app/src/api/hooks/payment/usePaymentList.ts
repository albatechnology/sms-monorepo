import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiPaymentIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Payment, mapPayment } from "types/Payment/Payment"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiPaymentIndexRequest,
  perPage = 10,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Payment[]>>(
    ["paymentList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .paymentIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Payment[] = res.data.data.map(mapPayment)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    { ...standardExtraQueryParam, ...extraProps },
  )

  return queryData
}
