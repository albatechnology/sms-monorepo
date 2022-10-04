import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { User } from "types/User"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type UserSetChannelMutationData = {
  channelId: User["channelId"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, UserSetChannelMutationData>(
    ({ channelId }: UserSetChannelMutationData) => {
      return api.userSetDefaultChannel({ channel: channelId.toString() })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            return queryClient.invalidateQueries("userLoggedIn")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
