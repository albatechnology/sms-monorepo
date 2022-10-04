import { V1Api } from "api/openapi"

import { ImageType, mapImages } from "types/Image"
import { UnwrapOpenAPIResponse } from "types/helper"

export type ProductVersion = {
  id: number
  name: string
  images: Nullable<ImageType[]>
  height: string
  width: string
  length: string
}

export const mapProductVersion = (
  apiObj: Partial<UnwrapOpenAPIResponse<V1Api["productVersion"]>[number]>,
): ProductVersion => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    images: !!apiObj.images ? mapImages(apiObj.images) : null,
    width: apiObj.width,
    height: apiObj.height,
    length: apiObj.length,
  }
}
