import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Customer, mapCustomer } from "types/Customer"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  customerId: number,
  extraProps?: UseQueryOptions<any, any, any>,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Customer, CustomAxiosErrorType>(
    ["customer", customerId],
    () => {
      return api
        .customerShow({ customer: customerId.toString() })
        .then((res) => {
          const data: Customer = mapCustomer(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    extraProps,
  )

  return queryData
}
