import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { V1ApiUserSupervisorTypesRequest } from "api/openapi"

import { SupervisorType, mapSupervisorType } from "types/SupervisorType"

import standardErrorHandling from "../../errors"

export default (requestObject?: V1ApiUserSupervisorTypesRequest) => {
  const api = useApi()

  const queryData = useQuery<SupervisorType[]>(
    ["supervisorTypeList", requestObject],
    () => {
      return api
        .userSupervisorTypes(requestObject)
        .then((res) => {
          const items: SupervisorType[] = res.data.data.map(mapSupervisorType)
          return items
        })
        .catch(standardErrorHandling)
    },
  )
  return queryData
}
