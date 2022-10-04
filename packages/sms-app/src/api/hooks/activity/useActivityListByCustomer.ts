import useApi from "hooks/useApi"
import useInfiniteQuery from "hooks/useInfiniteQuery"

import { V1ApiCustomerGetActivitiesRequest } from "api/openapi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import { Activity, mapActivity } from "types/Activity"

import standardErrorHandling from "../../errors"

export default (
  customerId: number,
  requestObject?: Partial<V1ApiCustomerGetActivitiesRequest>,
  perPage = 10,
) => {
  const api = useApi()

  const queryData = useInfiniteQuery<Paginated<Activity[]>>(
    ["activityListByCustomer", customerId, requestObject, perPage],
    ({ pageParam = 1 }) => {
      return api
        .customerGetActivities({
          customer: customerId,
          perPage,
          page: pageParam,
          ...requestObject,
        })
        .then((res) => {
          const items: Activity[] = res.data.data.map(mapActivity)
          return { ...res.data, data: items }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )

  return queryData
}
