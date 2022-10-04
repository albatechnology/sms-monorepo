import { ProductCategoryType } from "api/generated/enums"
import { V1Api } from "api/openapi"

import { ImageType, mapImages } from "types/Image"
import { UnwrapOpenAPIResponse } from "types/helper"

export type ProductCategory = {
  id: number
  name: string
  slug: string
  description: string
  images: Nullable<ImageType[]>
  level: number
  type: ProductCategoryType
  parentId: number
}

export const mapProductCategory = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productCategoryShow"]>,
): ProductCategory => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    slug: apiObj.slug,
    description: apiObj.description,
    images: !!apiObj.images ? mapImages(apiObj.images) : null,
    level: apiObj.level,
    type: apiObj.type,
    parentId: apiObj.parent_id,
  }
}

export const mapProductCategories = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productCategoryShow"]>[],
): ProductCategory[] => {
  return apiObj.map(mapProductCategory)
}
