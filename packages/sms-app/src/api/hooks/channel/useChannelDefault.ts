import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import { Channel, mapChannel } from "types/Channel"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (customErrorHandler?: (error: CustomAxiosErrorType) => any) => {
  const api = useApi()
  const { data } = useAuth()

  const queryData = useQuery<Channel, CustomAxiosErrorType>(
    ["channelDefault", data],
    () => {
      return api
        .channelDefault()
        .then((res) => {
          const data: Channel = mapChannel(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
