import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { QATopic, mapQATopic } from "types/QATopic"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<QATopic, CustomAxiosErrorType>(
    ["qaTopic", id],
    () => {
      return api
        .qaTopicShow({ qaTopic: id.toString() })
        .then((res) => {
          const data: QATopic = mapQATopic(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { enabled: !!id },
  )

  return queryData
}
