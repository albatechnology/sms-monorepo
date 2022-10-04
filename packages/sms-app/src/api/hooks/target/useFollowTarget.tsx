import { useQuery } from "react-query"

import useApi, { useAxios } from "hooks/useApi"

import { Paginated, standardExtraQueryParam } from "helper/pagination"

import standardErrorHandling from "../../errors"

export default (requestObject, perPage = 20) => {
  const axios = useAxios()
  const queryData = useQuery<any>(
    ["userList", requestObject, perPage],
    ({ pageParam = 1 }) => {
      return axios
        .get(`leads/list`, {
          params: {
            ...requestObject,
          },
        })
        .then((res) => {
          return { data: res.data.data }
        })
        .catch(standardErrorHandling)
    },
    standardExtraQueryParam,
  )
  return queryData
}
