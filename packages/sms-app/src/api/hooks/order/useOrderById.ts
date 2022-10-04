import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Order, mapOrder } from "types/Order"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  orderId: number,
  extraProps?: UseQueryOptions<any, any, any>,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Order, CustomAxiosErrorType>(
    ["order", orderId],
    () => {
      return api
        .orderShow({ order: orderId.toString() })
        .then((res) => {
          const data: Order = mapOrder(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { ...extraProps },
  )

  return queryData
}
