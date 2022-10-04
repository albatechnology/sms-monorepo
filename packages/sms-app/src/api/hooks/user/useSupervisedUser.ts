import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiUserSupervisedRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { User, mapUser } from "types/User"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiUserSupervisedRequest, perPage = 20) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<User[]>, string>(
    ["supervisedUser", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .userSupervised({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: User[] = res.data.data.map(mapUser)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )
  return queryData
}
