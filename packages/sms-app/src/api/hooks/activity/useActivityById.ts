import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Activity, mapActivity } from "types/Activity"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  activityId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Activity, CustomAxiosErrorType>(
    ["activity", activityId],
    () => {
      return api
        .activityShow({ activity: activityId.toString() })
        .then((res) => {
          const data: Activity = mapActivity(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
