import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "types/helper"

export type ProductCategoryCode = {
  id: number
  name: string
}

export const mapProductCategoryCode = (
  apiObj: Partial<UnwrapOpenAPIResponse<V1Api["productCategoryCodes"]>[number]>,
): ProductCategoryCode => {
  return {
    id: apiObj.id,
    name: apiObj.name,
  }
}
