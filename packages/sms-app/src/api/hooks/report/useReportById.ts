import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Report, mapReport } from "types/Report"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  id: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Report, CustomAxiosErrorType>(
    ["report", id],
    () => {
      return api
        .reportShow({ report: id.toString() })
        .then((res) => {
          const data: Report = mapReport(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { enabled: !!id },
  )

  return queryData
}
