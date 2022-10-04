import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Address, mapAddress } from "types/Address"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Address, CustomAxiosErrorType>(
    ["address", id],
    () => {
      return api
        .addressShow({ address: id.toString() })
        .then((res) => {
          const data: Address = mapAddress(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { enabled: !!id },
  )

  return queryData
}
