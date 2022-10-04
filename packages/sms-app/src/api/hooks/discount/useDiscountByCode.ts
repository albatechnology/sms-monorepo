import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Discount, mapDiscount } from "types/Discount"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  discountCode: string,
  extraProps?: UseQueryOptions<any, any, any>,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Discount, CustomAxiosErrorType>(
    ["discount", discountCode],
    () => {
      return api
        .discountGetByCode({ code: discountCode })
        .then((res) => {
          const data: Discount = mapDiscount(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    extraProps,
  )

  return queryData
}
