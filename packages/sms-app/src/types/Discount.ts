import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type Discount = {
  id: number
  name: string
  description: string
  minOrderPrice: Nullable<number>
  maxDiscountPricePerOrder: Nullable<number>
}

export const mapDiscount = (
  apiObj: UnwrapOpenAPIResponse<V1Api["discountGetByCode"]>,
): Discount => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    description: apiObj.description,
    minOrderPrice: apiObj.min_order_price,
    maxDiscountPricePerOrder: apiObj.max_discount_price_per_order,
  }
}
