import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import { V1ApiUserIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { User, mapUser } from "types/User"

import standardErrorHandling from "../../errors"
import useUserLoggedInData from "./useUserLoggedInData"

export default (requestObject?: V1ApiUserIndexRequest, perPage = 20) => {
  const api = useApi()

  const {
    queries: [{ data: userData }],
    meta,
  } = useMultipleQueries([useUserLoggedInData()] as const)

  const queryData = useInfiniteQuery<Paginated<User[]>, string>(
    ["userList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .userIndex({
          perPage,
          page: pageParam,
          ...requestObject,
          filterDescendantOf: userData.id,
        })
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
