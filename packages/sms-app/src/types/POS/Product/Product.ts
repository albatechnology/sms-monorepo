import { V1Api } from "api/openapi"

import { ImageType, mapImages } from "types/Image"
import { mapProductModel, ProductModel } from "types/POS/Product/ProductModel"
import { Brand, mapBrand } from "types/POS/ProductCategorization/Brand"
import { UnwrapOpenAPIResponse } from "types/helper"

import {
  mapProductCategories,
  ProductCategory,
} from "../ProductCategorization/ProductCategory"
import { mapProductTags, ProductTag } from "../ProductCategorization/ProductTag"
import {
  mapProductCategoryCode,
  ProductCategoryCode,
} from "./ProductCategoryCode"
import { mapProductVersion, ProductVersion } from "./ProductVersion"

export type Product = {
  id: number
  name: string
  categories: Nullable<ProductCategory[]>
  tags: Nullable<ProductTag[]>
  images: Nullable<ImageType[]>
  brand: Nullable<Brand>
  model: Nullable<ProductModel>
  version: Nullable<ProductVersion>
  categoryCode: Nullable<ProductCategoryCode>
}

export const mapProduct = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productShow"]>,
): Product => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    categories: !!apiObj.categories
      ? mapProductCategories(apiObj.categories)
      : null,
    tags: !!apiObj.tags ? mapProductTags(apiObj.tags) : null,
    images: !!apiObj.images ? mapImages(apiObj.images) : null,
    brand: !!apiObj.brand ? mapBrand(apiObj.brand) : null,
    model: !!apiObj.model ? mapProductModel(apiObj.model) : null,
    version: !!apiObj.version ? mapProductVersion(apiObj.version) : null,
    categoryCode: !!apiObj.category_code
      ? mapProductCategoryCode(apiObj.category_code)
      : null,
  }
}
