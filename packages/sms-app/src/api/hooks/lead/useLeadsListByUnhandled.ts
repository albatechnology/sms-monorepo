import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiLeadUnhandledIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Lead, mapLead } from "types/Lead"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiLeadUnhandledIndexRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Lead[]>>(
    ["leadListByUnhandled", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .leadUnhandledIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Lead[] = res.data.data.map(mapLead)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )
  return queryData
}
