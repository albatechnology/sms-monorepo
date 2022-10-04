import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { QATopic } from "types/QATopic"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateQATopicMutationData = {
  subject: QATopic["subject"]
  users: number[]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateQATopicMutationData>(
    ({ subject, users }: CreateQATopicMutationData) => {
      return api.qaTopicStore({
        data: {
          subject,
          users: users,
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("QA Topic berhasil dibuat")

            queryClient.invalidateQueries("qaTopicList")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
