import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type Channel = {
  id: number
  name: string
  company_id: number
}

export const mapChannel = (
  apiObj: UnwrapOpenAPIResponse<V1Api["channelShow"]>,
): Channel => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    company_id: apiObj.company_id,
  }
}
