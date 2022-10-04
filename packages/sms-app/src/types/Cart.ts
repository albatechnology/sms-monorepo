import { V1Api } from "api/openapi"

import {
  ProductUnitColor,
  mapProductUnitColor,
} from "types/POS/ProductUnit/ProductUnitColor"
import {
  ProductUnitCovering,
  mapProductUnitCovering,
} from "types/POS/ProductUnit/ProductUnitCovering"

import { UnwrapOpenAPIResponse } from "./helper"

export type Cart = {
  items: Array<CartItem>
  totalPrice: number
}

export type CartItem = {
  sku: string
  id: number
  quantity: number
  name: string
  unitPrice: number
  totalPrice: number
  totalDiscount: number
  colour: ProductUnitColor
  covering: ProductUnitCovering
}

export const mapCartItem = (
  apiObj: UnwrapOpenAPIResponse<V1Api["cartIndex"]>["items"][number],
): CartItem => {
  return {
    id: apiObj.id,
    quantity: apiObj.quantity,
    name: apiObj.name,
    sku: apiObj.sku,
    unitPrice: apiObj.unit_price,
    totalPrice: apiObj.total_price,
    totalDiscount: apiObj.total_discount,
    colour: apiObj.colour ? mapProductUnitColor(apiObj.colour) : null,
    covering: apiObj.covering ? mapProductUnitCovering(apiObj.covering) : null,
  }
}

export const mapCart = (
  apiObj: UnwrapOpenAPIResponse<V1Api["cartIndex"]>,
): Cart => {
  return {
    items: apiObj.items ? apiObj.items.map(mapCartItem) : null,
    totalPrice: apiObj.total_price,
  }
}
