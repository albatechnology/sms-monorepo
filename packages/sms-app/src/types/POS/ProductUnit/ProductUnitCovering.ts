import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "types/helper"

export type ProductUnitCovering = {
  id: number
  name: string
}

export const mapProductUnitCovering = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productUnitCoverings"]>[number],
): ProductUnitCovering => {
  return {
    id: apiObj.id,
    name: apiObj.name,
  }
}
