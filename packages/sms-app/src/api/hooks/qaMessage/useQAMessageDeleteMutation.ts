import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { QAMessage } from "types/QAMessage"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type DeleteQAMessageMutationData = {
  id: QAMessage["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, DeleteQAMessageMutationData>(
    ({ id }: DeleteQAMessageMutationData) => {
      return api.qaMessageDestroy({ qaMessage: id.toString() })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("QA Topic berhasil dihapus")

            queryClient.invalidateQueries("qaMessageList")
            queryClient.invalidateQueries(["qaMessage", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
