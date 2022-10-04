import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiPaymentCategoryIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import {
  PaymentCategory,
  mapPaymentCategory,
} from "types/Payment/PaymentCategory"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiPaymentCategoryIndexRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<PaymentCategory[]>>(
    ["paymentCategoryList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .paymentCategoryIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: PaymentCategory[] = res.data.data.map(mapPaymentCategory)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
