import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiCompanyIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Company, mapCompany } from "types/Company"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiCompanyIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Company[]>>(
    ["companyList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .companyIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Company[] = res.data.data.map(mapCompany)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
