import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { ActivityComment, mapActivityComment } from "types/ActivityComment"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<ActivityComment, CustomAxiosErrorType>(
    ["activityComment", id],
    () => {
      return api
        .activityCommentShow({ activityComment: id.toString() })
        .then((res) => {
          const data: ActivityComment = mapActivityComment(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { enabled: !!id },
  )

  return queryData
}
