import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { User, mapUser } from "types/User"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  extraProps?: UseQueryOptions<any, any, any>,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<User, CustomAxiosErrorType>(
    ["user", id],
    () => {
      return api
        .userShow({ user: id.toString() })
        .then((res) => {
          const data: User = mapUser(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { ...extraProps },
  )

  return queryData
}
