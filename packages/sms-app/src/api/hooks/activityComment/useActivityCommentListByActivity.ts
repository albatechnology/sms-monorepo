import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiActivityGetCommentsRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { ActivityComment, mapActivityComment } from "types/ActivityComment"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiActivityGetCommentsRequest,
  perPage = 5,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<ActivityComment[]>>(
    ["activityCommentListByActivity", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .activityGetComments({ perPage, page: pageParam, ...requestObject })
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
