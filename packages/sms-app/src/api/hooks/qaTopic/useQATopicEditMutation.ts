import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { QATopic } from "types/QATopic"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditQATopicMutationData = {
  id: QATopic["id"]
  subject: QATopic["subject"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditQATopicMutationData>(
    ({ id, subject }: EditQATopicMutationData) => {
      return api.qaTopicUpdate({
        qaTopic: id.toString(),
        data: {
          subject,
        },
      })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("QA Topic berhasil dirubah")

            queryClient.invalidateQueries("qaTopicList")
            queryClient.invalidateQueries(["qaTopic", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
