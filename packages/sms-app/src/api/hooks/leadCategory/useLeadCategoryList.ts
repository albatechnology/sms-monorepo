import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiLeadCategoriesRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import standardErrorHandling from "../../errors"
import { LeadCategory, mapLeadCategory } from "./../../../types/LeadCategory"

export default (requestObject?: V1ApiLeadCategoriesRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<LeadCategory[]>>(
    ["leadCategoriesList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .leadCategories({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: LeadCategory[] = res.data.data.map(mapLeadCategory)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
