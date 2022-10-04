import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { QAMessage, mapQAMessage } from "types/QAMessage"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<QAMessage, CustomAxiosErrorType>(
    ["qaMessage", id],
    () => {
      return api
        .qaMessageShow({ qaMessage: id.toString() })
        .then((res) => {
          const data: QAMessage = mapQAMessage(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { enabled: !!id },
  )

  return queryData
}
