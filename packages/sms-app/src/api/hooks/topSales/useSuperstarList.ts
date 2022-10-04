import { useQuery } from "react-query"

import useApi, { useAxios } from "hooks/useApi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import standardErrorHandling from "../../errors"

export default (
  value?: string,
  type?: string,
  date?: string,
  endDate?: string,
  perPage = 20,
) => {
  const axios = useAxios()
  const queryData = useQuery<any>(
    ["userList", type, date, value, perPage],
    ({ pageParam = 1 }) => {
      return axios
        .get(`dashboard/index-top-sales/${value}`, {
          params: {
            limit: 5,
            start_at: date,
            end_at: endDate,
          },
        })
        .then((res) => {
          return { data: res.data }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )
  return queryData
}
