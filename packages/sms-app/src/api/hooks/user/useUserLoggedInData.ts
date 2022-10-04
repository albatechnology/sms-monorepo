import { useQuery, UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { User, mapUser } from "types/User"

import standardErrorHandling from "../../errors"

export default (
  shouldRun: boolean = true,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const { data } = useAuth()

  const queryData = useQuery<User, any>(
    ["userLoggedIn", data.jwt],
    () => {
      return api
        .userMe()
        .then((res) => {
          const data = res.data.data
          const user: User = mapUser(data)
          return user
        })
        .catch(standardErrorHandling)
    },
    { enabled: shouldRun, ...extraProps },
  )

  return queryData
}
