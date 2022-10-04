import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "types/helper"

import { mapProductUnitColor, ProductUnitColor } from "./ProductUnitColor"
import {
  mapProductUnitCovering,
  ProductUnitCovering,
} from "./ProductUnitCovering"

export type ProductUnit = {
  id: number
  name: string
  description: string
  price: number
  colour: Nullable<ProductUnitColor>
  covering: Nullable<ProductUnitCovering>
  productionCost: number
  sku: string
}

export const mapProductUnit = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productUnitShow"]>,
): ProductUnit => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    description: apiObj.description,
    price: apiObj.price,
    sku: apiObj.sku,
    colour: !!apiObj.colour ? mapProductUnitColor(apiObj.colour) : null,
    covering: !!apiObj.covering
      ? mapProductUnitCovering(apiObj.covering)
      : null,
    productionCost: apiObj.production_cost,
  }
}
