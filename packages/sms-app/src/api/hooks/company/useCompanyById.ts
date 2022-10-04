import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Company, mapCompany } from "types/Company"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  companyId: number,
  extraProps?: UseQueryOptions<any, any, any>,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Company, CustomAxiosErrorType>(
    ["company", companyId],
    () => {
      return api
        .companyShow({ company: companyId.toString() })
        .then((res) => {
          const data: Company = mapCompany(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { ...extraProps },
  )

  return queryData
}
