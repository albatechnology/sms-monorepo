import { AddressType } from "api/generated/enums"
import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type Address = {
  id: number
  addressLine1: string
  addressLine2: Nullable<string>
  addressLine3: Nullable<string>
  postcode: Nullable<string>
  city: Nullable<string>
  country: Nullable<string>
  province: Nullable<string>
  phone: Nullable<string>
  type: AddressType
  customerId: number
}

export const mapAddress = (
  apiObj: UnwrapOpenAPIResponse<V1Api["addressShow"]>,
): Address => {
  return {
    id: apiObj.id,
    addressLine1: apiObj.address_line_1,
    addressLine2: apiObj.address_line_2,
    addressLine3: apiObj.address_line_3,
    postcode: apiObj.postcode,
    city: apiObj.city,
    province: apiObj.province,
    country: apiObj.country,
    phone: apiObj.phone,
    type: apiObj.type,
    customerId: apiObj.customer_id,
  }
}
