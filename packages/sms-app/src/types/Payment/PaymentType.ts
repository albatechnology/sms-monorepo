import { V1Api } from "api/openapi"

import { ImageType, mapImages } from "types/Image"
import { UnwrapOpenAPIResponse } from "types/helper"

export type PaymentType = {
  id: number
  name: string
  paymentCategoryId: number
  images: ImageType[]
}

export const mapPaymentType = (
  apiObj: UnwrapOpenAPIResponse<V1Api["paymentTypeIndex"]>[number],
): PaymentType => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    paymentCategoryId: apiObj.payment_category_id,
    images: apiObj.images ? mapImages(apiObj.images) : null,
  }
}
