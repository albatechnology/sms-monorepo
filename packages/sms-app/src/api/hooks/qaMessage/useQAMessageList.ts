import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiQaTopicGetQaMessagesRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { QAMessage, mapQAMessage } from "types/QAMessage"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiQaTopicGetQaMessagesRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<QAMessage[]>>(
    ["qaMessageList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .qaTopicGetQaMessages({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: QAMessage[] = res.data.data.map(mapQAMessage)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
