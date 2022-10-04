import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Payment, mapPayment } from "types/Payment/Payment"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  extraProps?: UseQueryOptions<any, any, any>,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Payment, CustomAxiosErrorType>(
    ["payment", id],
    () => {
      return api
        .paymentShow({ payment: id.toString() })
        .then((res) => {
          const data: Payment = mapPayment(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { enabled: !!id, ...extraProps },
  )

  return queryData
}
