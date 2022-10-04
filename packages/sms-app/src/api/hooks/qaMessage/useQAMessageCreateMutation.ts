import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { QAMessage } from "types/QAMessage"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateQAMessageMutationData = {
  topicId: QAMessage["topic"]["id"]
  content: QAMessage["content"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateQAMessageMutationData>(
    ({ topicId, content }: CreateQAMessageMutationData) => {
      return api.qaMessageStore({
        data: {
          topic_id: topicId,
          content,
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            queryClient.invalidateQueries("qaMessageList")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
