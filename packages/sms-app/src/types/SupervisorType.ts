import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type SupervisorType = {
  id: number
  name: string
  level: number | null
}

export const mapSupervisorType = (
  apiObj: UnwrapOpenAPIResponse<V1Api["userSupervisorTypes"]>[number],
): SupervisorType => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    level: apiObj.level,
  }
}
