import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "types/helper"

import { ImageType, mapImages } from "./../../Image"

export type Brand = {
  id: number
  name: string
  images: Nullable<ImageType[]>
  estimated: number
}

export const mapBrand = (
  apiObj: Partial<UnwrapOpenAPIResponse<V1Api["productBrand"]>[number]>,
): Brand => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    images: !!apiObj.images ? mapImages(apiObj.images) : null,
    estimated: apiObj.estimated_value,
  }
}
