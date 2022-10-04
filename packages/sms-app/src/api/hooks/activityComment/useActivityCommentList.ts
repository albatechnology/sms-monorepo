import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiActivityCommentIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { ActivityComment, mapActivityComment } from "types/ActivityComment"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiActivityCommentIndexRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ActivityComment[]>>(
    ["activityCommentList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .activityCommentIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: ActivityComment[] = res.data.data.map(mapActivityComment)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
