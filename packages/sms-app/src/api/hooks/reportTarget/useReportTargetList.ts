import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { V1ApiTargetIndexRequest } from "api/openapi"

import { ReportTarget, mapReportTarget } from "types/ReportTarget"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiTargetIndexRequest,
  perPage = 30,
  extraProps?: UseQueryOptions<any, any, any>,
) => {
  const api = useApi()

  const queryData = useQuery<ReportTarget[]>(
    ["reportTargetList", requestObject, perPage],
    () => {
      return api
        .targetIndex({
          ...requestObject,
        })
        .then((res) => {
          const items: ReportTarget[] = res.data.data.map(mapReportTarget)
          return items
        })
        .catch(standardErrorHandling)
    },
    extraProps,
  )

  return queryData
}
