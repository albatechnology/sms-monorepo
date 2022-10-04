import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { QATopic } from "types/QATopic"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type DeleteQATopicMutationData = {
  id: QATopic["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, DeleteQATopicMutationData>(
    ({ id }: DeleteQATopicMutationData) => {
      return api.qaTopicDestroy({ qaTopic: id.toString() })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("QA Topic berhasil dihapus")

            queryClient.invalidateQueries("qaTopicList")
            queryClient.invalidateQueries(["qaTopic", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
