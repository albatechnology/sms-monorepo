import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "types/helper"

export type ProductTag = {
  id: number
  name: string
  slug: string
}

export const mapProductTag = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productTagShow"]>,
): ProductTag => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    slug: apiObj.slug,
  }
}

export const mapProductTags = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productTagShow"]>[],
): ProductTag[] => {
  return apiObj.map(mapProductTag)
}
