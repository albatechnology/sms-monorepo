import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Channel, mapChannel } from "types/Channel"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  channelId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Channel, CustomAxiosErrorType>(
    ["channel", channelId],
    () => {
      return api
        .channelShow({ channel: channelId.toString() })
        .then((res) => {
          const data: Channel = mapChannel(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
