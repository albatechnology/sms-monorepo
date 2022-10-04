import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type LeadCategory = {
  id: number
  name: string
  description: string
}

export const mapLeadCategory = (
  apiObj: UnwrapOpenAPIResponse<V1Api["leadCategories"]>[number],
): LeadCategory => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    description: apiObj.description,
  }
}
