import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Lead, mapLead } from "types/Lead"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

export default (
  leadId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Lead, CustomAxiosErrorType>(
    ["lead", leadId],
    () => {
      return api
        .leadShow({ lead: leadId.toString() })
        .then((res) => {
          const data: Lead = mapLead(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
