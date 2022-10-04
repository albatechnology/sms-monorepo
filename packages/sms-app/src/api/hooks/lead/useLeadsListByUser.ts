import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { useAuth } from "providers/Auth"

import { V1ApiLeadIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Lead, mapLead } from "types/Lead"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiLeadIndexRequest, perPage = 10) => {
  const api = useApi()
  const { data } = useAuth()

  const queryData = useInfiniteQuery<Paginated<Lead[]>>(
    ["leadListByUser", data, requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .leadIndex({ perPage, page: pageParam, ...requestObject })
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
