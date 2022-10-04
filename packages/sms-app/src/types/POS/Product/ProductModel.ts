import { V1Api } from "api/openapi"

import { ImageType, mapImages } from "types/Image"
import { UnwrapOpenAPIResponse } from "types/helper"

export type ProductModel = {
  id: number
  name: string
  description: string
  images: Nullable<ImageType[]>
  priceMin: Nullable<number>
  priceMax: Nullable<number>
}

export const mapProductModel = (
  apiObj: Partial<UnwrapOpenAPIResponse<V1Api["productModel"]>[number]>,
): ProductModel => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    description: apiObj.description,
    images: !!apiObj.images ? mapImages(apiObj.images) : null,
    priceMin: apiObj.price_min,
    priceMax: apiObj.price_max,
  }
}
