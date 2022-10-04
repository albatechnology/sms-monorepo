import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiReportIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Report, mapReport } from "types/Report"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiReportIndexRequest, perPage = 30) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Report[]>>(
    ["reportList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .reportIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Report[] = res.data.data.map(mapReport)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
