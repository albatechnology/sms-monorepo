import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { ActivityComment } from "types/ActivityComment"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type DeleteActivityCommentMutationData = {
  id: ActivityComment["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, DeleteActivityCommentMutationData>(
    ({ id }: DeleteActivityCommentMutationData) => {
      return api.activityCommentDestroy({ activityComment: id.toString() })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Activity Comment berhasil dihapus")

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
