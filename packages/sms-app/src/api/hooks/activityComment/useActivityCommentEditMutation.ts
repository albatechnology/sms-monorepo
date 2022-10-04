import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { ActivityComment } from "types/ActivityComment"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditActivityCommentMutationData = {
  id: ActivityComment["id"]
  content: ActivityComment["content"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditActivityCommentMutationData>(
    ({ id, content }: EditActivityCommentMutationData) => {
      return api.activityCommentUpdate({
        activityComment: id.toString(),
        data: {
          content,
        },
      })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Activity Comment berhasil dirubah")

            queryClient.invalidateQueries("activityCommentList")
            queryClient.invalidateQueries("activityCommentListByActivity")
            queryClient.invalidateQueries([
              "activityComment",
              passedVariables.id,
            ])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
