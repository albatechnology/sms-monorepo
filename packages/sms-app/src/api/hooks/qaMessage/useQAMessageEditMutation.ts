import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { QAMessage } from "types/QAMessage"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditQAMessageMutationData = {
  id: QAMessage["id"]
  content: QAMessage["content"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditQAMessageMutationData>(
    ({ id, content }: EditQAMessageMutationData) => {
      return api.qaMessageUpdate({
        qaMessage: id.toString(),
        data: {
          content,
        },
      })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("QA Topic berhasil dirubah")

            queryClient.invalidateQueries("qaMessageList")
            queryClient.invalidateQueries(["qaMessage", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
