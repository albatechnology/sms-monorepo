import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Promo, mapPromo } from "types/Promo"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Promo, CustomAxiosErrorType>(
    ["promo", id],
    () => {
      return api
        .promoShow({ promo: id.toString() })
        .then((res) => {
          const data: Promo = mapPromo(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { enabled: !!id },
  )

  return queryData
}
