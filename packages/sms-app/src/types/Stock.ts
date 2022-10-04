import { V1Api } from "api/openapi"

import { Channel, mapChannel } from "./Channel"
import { Brand, mapBrand } from "./POS/ProductCategorization/Brand"
import { ProductUnit, mapProductUnit } from "./POS/ProductUnit/ProductUnit"
import {
  mapProductUnitColor,
  ProductUnitColor,
} from "./POS/ProductUnit/ProductUnitColor"
import {
  mapProductUnitCovering,
  ProductUnitCovering,
} from "./POS/ProductUnit/ProductUnitCovering"
import { UnwrapOpenAPIResponse } from "./helper"

export type Stock = {
  id: number
  stock: Number
  channel: Channel
  productUnit: Omit<ProductUnit, "colour" | "covering">
  colour: ProductUnitColor
  covering: ProductUnitCovering
  productBrand: Brand
  updatedAt: Date
  createdAt: Date
  indent: Number
  outstanding_order: Number
  outstanding_shipment: Number
}

export const mapStock = (
  apiObj: UnwrapOpenAPIResponse<V1Api["stockIndexExtended"]>[number],
): Stock => {
  return {
    id: apiObj.id,
    stock: apiObj.stock,
    channel: mapChannel(apiObj.channel),
    productUnit: mapProductUnit(apiObj.product_unit),
    colour: apiObj?.colour ? mapProductUnitColor(apiObj.colour) : null,
    covering: apiObj?.covering ? mapProductUnitCovering(apiObj.covering) : null,
    productBrand: mapBrand(apiObj.product_brand),
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
    indent: apiObj.indent,
    outstanding_order: apiObj.outstanding_order,
    outstanding_shipment: apiObj.outstanding_shipment,
  }
}
