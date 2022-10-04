import useApi, { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Activity, mapActivity } from "types/Activity"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (activityId: number) => {
  const api = useAxios()

  const queryData = useQuery<Activity, CustomAxiosErrorType>(
    ["activity", activityId],
    () => {
      return api
        .get(`activities/active/${activityId}`)
        .then((res) => {
          const data = res.data
          return data
        })
        .catch(standardErrorHandling)
    },
  )

  return queryData
}
