import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiChannelIndexRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Channel, mapChannel } from "types/Channel"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiChannelIndexRequest, perPage = 10) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Channel[]>>(
    ["channelList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .channelIndex({ perPage, page: pageParam, ...requestObject })
        .then((res) => {
          const items: Channel[] = res.data.data.map(mapChannel)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
