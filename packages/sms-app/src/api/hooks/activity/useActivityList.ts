import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiActivityIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Activity, mapActivity } from "types/Activity"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiActivityIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Activity[]>>(
    ["activityList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .activityIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Activity[] = res.data.data.map(mapActivity)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
