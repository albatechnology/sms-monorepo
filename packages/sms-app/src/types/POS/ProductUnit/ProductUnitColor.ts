import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "types/helper"

export type ProductUnitColor = {
  id: number
  name: string
}

export const mapProductUnitColor = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productUnitColours"]>[number],
): ProductUnitColor => {
  return {
    id: apiObj.id,
    name: apiObj.name,
  }
}
