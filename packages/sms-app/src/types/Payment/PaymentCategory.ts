import { V1Api } from "api/openapi"

import { ImageType, mapImages } from "types/Image"
import { UnwrapOpenAPIResponse } from "types/helper"

export type PaymentCategory = {
  id: number
  name: string
  images: ImageType[]
}

export const mapPaymentCategory = (
  apiObj: UnwrapOpenAPIResponse<V1Api["paymentCategoryIndex"]>[number],
): PaymentCategory => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    images: apiObj.images ? mapImages(apiObj.images) : null,
  }
}
