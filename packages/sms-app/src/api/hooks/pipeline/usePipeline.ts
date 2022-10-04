import moment from "moment"
import { useQuery } from "react-query"

import useApi, { useAxios } from "hooks/useApi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import standardErrorHandling from "../../errors"

export default (startDate, endDate, company, channel, perPage = 20) => {
  const axios = useAxios()
  const queryData = useQuery<any>(
    ["PipelineHooks", startDate, endDate, company, channel, perPage],
    ({ pageParam = 1 }) => {
      return axios
        .get(`dashboard/report-leads-optimized`, {
          params: {
            start_date: moment(startDate).format("YYYY-MM-DD"),
            end_date: moment(endDate).format("YYYY-MM-DD"),
            company_id: company,
            channel_id: channel,
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
