import moment from "moment"
import { useQuery } from "react-query"

import useApi, { useAxios } from "hooks/useApi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import standardErrorHandling from "../../errors"

export default (startDate, company, perPage = 20) => {
  const axios = useAxios()
  const queryData = useQuery<any>(
    ["ActivityTotalQuery", startDate, company, perPage],
    ({ pageParam = 1 }) => {
      return axios
        .get(`activities/report/detail`, {
          params: {
            start_at: moment(startDate).format("YYYY-MM-DD"),
            end_at: moment(startDate).endOf("month").format("YYYY-MM-DD"),
            company_id: company,
            per_page: 100,
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
