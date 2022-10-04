import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiPaymentTypeIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { PaymentType, mapPaymentType } from "types/Payment/PaymentType"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiPaymentTypeIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<PaymentType[]>>(
    ["paymentTypeList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .paymentTypeIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: PaymentType[] = res.data.data.map(mapPaymentType)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
