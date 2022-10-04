import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiQaTopicIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { QATopic, mapQATopic } from "types/QATopic"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiQaTopicIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<QATopic[]>>(
    ["qaTopicList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .qaTopicIndex({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: QATopic[] = res.data.data.map(mapQATopic)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
