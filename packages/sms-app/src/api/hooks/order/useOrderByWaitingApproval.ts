import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiOrderIndexWaitingApprovalRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Order, mapOrder } from "types/Order"

import standardErrorHandling from "../../errors"

export default (
  requestObject?: V1ApiOrderIndexWaitingApprovalRequest,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Order[]>>(
    ["orderByWaitingApproval", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .orderListApproval({
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Order[] = res.data.data.map(mapOrder)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
