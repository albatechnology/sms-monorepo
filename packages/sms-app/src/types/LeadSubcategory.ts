import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type SubLeadCategory = {
  id: number
  name: string
  description: string
}

export const mapSubLeadCategory = (
  apiObj: UnwrapOpenAPIResponse<V1Api["subLeadCategories"]>[number],
): SubLeadCategory => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    description: apiObj.description,
  }
}
