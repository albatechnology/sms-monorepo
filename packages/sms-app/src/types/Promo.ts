import { V1Api } from "api/openapi"

import { Company, mapCompany } from "./Company"
import { ImageType, mapImages } from "./Image"
import { UnwrapOpenAPIResponse } from "./helper"

export type Promo = {
  id: number
  name: string
  description: string | null
  startTime: Date
  endTime: Date | null
  images?: ImageType[]
  leadCategoryId: number | null
  company?: Company
}

export const mapPromo = (
  apiObj: UnwrapOpenAPIResponse<V1Api["promoShow"]>,
): Promo => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    description: apiObj.description,
    startTime: new Date(apiObj.start_time),
    endTime: new Date(apiObj.end_time),
    images: apiObj.images ? mapImages(apiObj.images) : null,
    leadCategoryId: apiObj.lead_category_id,
    company: apiObj.company ? mapCompany(apiObj.company) : null,
  }
}
