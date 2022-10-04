import { PaymentStatus } from "api/generated/enums"
import { V1Api } from "api/openapi"

import {
  COLOR_STATUS_YELLOW_BG,
  COLOR_STATUS_YELLOW_TEXT,
  COLOR_STATUS_GREEN_BG,
  COLOR_STATUS_GREEN_TEXT,
  COLOR_STATUS_RED_BG,
  COLOR_STATUS_RED_TEXT,
} from "helper/theme"

import { ImageType, mapImages } from "types/Image"
import { mapUser, User } from "types/User"
import { UnwrapOpenAPIResponse } from "types/helper"

import { mapPaymentType, PaymentType } from "./PaymentType"

export type Payment = {
  id: number
  amount: number
  reference: string
  status: PaymentStatus
  paymentType: PaymentType
  addedBy: User
  orderId: number
  companyId: number
  proof: ImageType[]
  createdAt: Date
}

export const mapPayment = (
  apiObj: UnwrapOpenAPIResponse<V1Api["paymentShow"]>,
): Payment => {
  return {
    id: apiObj.id,
    amount: apiObj.amount,
    reference: apiObj.reference,
    status: apiObj.status,
    paymentType: apiObj.payment_type
      ? mapPaymentType(apiObj.payment_type)
      : null,
    addedBy: apiObj.added_by ? mapUser(apiObj.added_by) : null,
    orderId: apiObj.order_id,
    companyId: apiObj.company_id,
    proof: apiObj.proof ? mapImages(apiObj.proof) : null,
    createdAt: new Date(apiObj.created_at),
  }
}

type PaymentStatusConfigObject = {
  bg: string
  textColor: string
  displayText: string
}

export const paymentStatusConfig: Record<
  PaymentStatus,
  PaymentStatusConfigObject
> = {
  PENDING: {
    bg: COLOR_STATUS_YELLOW_BG,
    textColor: COLOR_STATUS_YELLOW_TEXT,
    displayText: "Pending",
  },
  APPROVED: {
    bg: COLOR_STATUS_GREEN_BG,
    textColor: COLOR_STATUS_GREEN_TEXT,
    displayText: "Approved",
  },
  REJECTED: {
    bg: COLOR_STATUS_RED_BG,
    textColor: COLOR_STATUS_RED_TEXT,
    displayText: "Rejected",
  },
}
