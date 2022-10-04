import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { ActivityComment } from "types/ActivityComment"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateActivityCommentMutationData = {
  activityCommentId: ActivityComment["activityCommentId"]
  activityId: ActivityComment["activityId"]
  content: ActivityComment["content"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateActivityCommentMutationData>(
    ({
      activityCommentId,
      activityId,
      content,
    }: CreateActivityCommentMutationData) => {
      return api.activityCommentStore({
        data: {
          activity_comment_id: activityCommentId,
          activity_id: activityId,
          content,
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            queryClient.invalidateQueries("activityCommentList")
            queryClient.invalidateQueries("activityCommentListByActivity")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
