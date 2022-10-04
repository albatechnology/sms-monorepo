import moment from "moment"
import { useQuery } from "react-query"

import useApi, { useAxios } from "hooks/useApi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import standardErrorHandling from "../../errors"

export default (name?: string, id?: number, perPage = 50) => {
  const axios = useAxios()
  const queryData = useQuery<any>(
    ["LocationStore", name, id, perPage],
    ({ pageParam = 1 }) => {
      return axios
        .get(`locations`, {
          params: {
            perPage: 50,
            sku: name,
            company_id: id,
          },
        })
        .then((res) => {
          console.log(res)
          return { data: res.data }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )
  return queryData
}
