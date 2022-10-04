import {
  OrderStatus,
  OrderPaymentStatus,
  DiscountError,
  OrderApprovalStatus,
} from "api/generated/enums"
import { V1Api } from "api/openapi"

import {
  COLOR_ORDER_PAYMENT_STATUS_DOWN_PAYMENT,
  COLOR_ORDER_PAYMENT_STATUS_NONE,
  COLOR_ORDER_PAYMENT_STATUS_OVER_PAYMENT,
  COLOR_ORDER_PAYMENT_STATUS_OVER_REFUNDED,
  COLOR_ORDER_PAYMENT_STATUS_PARTIAL,
  COLOR_ORDER_PAYMENT_STATUS_SETTLEMENT,
  COLOR_ORDER_STATUS_CANCELLED,
  COLOR_ORDER_STATUS_QUOTATION,
  COLOR_ORDER_STATUS_RETURNED,
  COLOR_ORDER_STATUS_SHIPMENT,
  COLOR_STATUS_CLOSED_BG,
  COLOR_STATUS_CLOSED_TEXT,
  COLOR_STATUS_GREEN_BG,
  COLOR_STATUS_GREEN_TEXT,
  COLOR_STATUS_RED_BG,
  COLOR_STATUS_RED_TEXT,
} from "helper/theme"

import { mapImages } from "types/Image"

import { Address, mapAddress } from "./Address"
import { Channel, mapChannel } from "./Channel"
import { Customer, mapCustomer } from "./Customer"
import { Discount, mapDiscount } from "./Discount"
import { mapUser, User } from "./User"
import { UnwrapOpenAPIResponse } from "./helper"

export type Order = {
  id: number
  originalPrice: number
  totalDiscount: number
  totalPrice: number
  shippingFee: number
  packingFee: number
  additionalDiscount: number
  amountPaid: number
  invoiceNumber: string
  status: OrderStatus
  paymentStatus: OrderPaymentStatus
  approvalStatus: OrderApprovalStatus
  discountError: DiscountError
  leadId: number
  userId: number
  channelId: number
  discountId: Nullable<number>
  user: User
  channel: Channel
  companyId: number
  customer: Customer
  orderDetails: any //DEBT
  orderDiscount: any
  cartDemand: any
  billingAddress: Address
  shippingAddress: Address
  discount: Discount
  note: string
  createdAt: Date
  updatedAt: Date
  expectedShippingDate: Date
  expectedValidQuotation?: Date
  paymentStatusForInvoice?: OrderPaymentStatus
  approvedBy?: User
  additional_discount_ratio?: number
  approvalNote?: string
  discountTakeoverBy?: Object
  limitPercentage?: number
  orlanNumber?: string
}

export const mapOrder = (
  apiObj: UnwrapOpenAPIResponse<V1Api["orderShow"]>,
): Order => {
  return {
    id: apiObj.id,
    originalPrice: apiObj.original_price,
    totalDiscount: apiObj.total_discount,
    totalPrice: apiObj.total_price,
    shippingFee: apiObj.shipping_fee,
    packingFee: apiObj.packing_fee,
    additionalDiscount: apiObj.additional_discount,
    amountPaid: apiObj.amount_paid,
    invoiceNumber: apiObj.invoice_number,
    status: apiObj.status,
    paymentStatus: apiObj.payment_status,
    approvalStatus: apiObj.approval_status,
    discountError: apiObj.discount_error,
    leadId: apiObj.lead_id,
    userId: apiObj.user_id,
    channelId: apiObj.channel_id,
    discountId: apiObj.discount_id,
    user: apiObj.user ? mapUser(apiObj.user) : null,
    channel: apiObj.channel ? mapChannel(apiObj.channel) : null,
    companyId: apiObj.company_id,
    customer: apiObj.customer ? mapCustomer(apiObj.customer) : null,
    orderDetails: apiObj.order_details
      ? apiObj.order_details.map(mapOrderDetails)
      : null,
    cartDemand: apiObj.cart_demand ? apiObj.cart_demand : null,
    billingAddress: apiObj.billing_address
      ? mapAddress(apiObj.billing_address)
      : null,
    shippingAddress: apiObj.shipping_address
      ? mapAddress(apiObj.shipping_address)
      : null,
    discount: apiObj.discount ? mapDiscount(apiObj.discount) : null,
    note: apiObj.note,
    createdAt: new Date(apiObj.created_at),
    updatedAt: new Date(apiObj.updated_at),
    expectedShippingDate: new Date(apiObj.expected_shipping_datetime),
    expectedValidQuotation: new Date(apiObj.quotation_valid_until_datetime),
    paymentStatusForInvoice: apiObj.payment_status_for_invoice,
    approvedBy: apiObj.approved_by ? mapUser(apiObj.approved_by) : null,
    additional_discount_ratio: apiObj.additional_discount_ratio,
    approvalNote: apiObj.approval_note,
    discountTakeoverBy: apiObj.discount_take_over_by,
    limitPercentage: apiObj.discount_approval_limit_percentage,
    orlanNumber: apiObj.orlan_tr_no,
  }
}

const mapOrderDetails = (
  apiObj: UnwrapOpenAPIResponse<V1Api["orderShow"]>["order_details"][number],
): any => {
  return {
    id: apiObj.id,
    quantity: apiObj.quantity,
    unitPrice: apiObj.unit_price,
    totalDiscount: apiObj.total_discount,
    totalPrice: apiObj.total_price,
    status: apiObj.status,
    productUnit: apiObj.product_unit,
    colour: apiObj.colour,
    covering: apiObj.covering,
    brand: apiObj.brand,
    model: apiObj.model,
    version: apiObj.version,
    categoryCode: apiObj.category_code,
    images: !!apiObj.images ? mapImages(apiObj.images) : null,
    photo: apiObj.photo ? mapImages(apiObj.photo) : null,
  }
}
// const mapCartDemand = (
//   apiObj: UnwrapOpenAPIResponse<V1Api["orderShow"]>["cart_demand"][number],
// ): any => {
//   return {
//     id: apiObj.id,
//     quantity: apiObj.quantity,
//     image : !!apiObj.image ? apiObj.image : null,
//     name: apiObj.name,
//     price : apiObj.price
//   }
// }

type OrderStatusConfigObject = {
  textColor: string
  displayText: string
}

export const orderStatusConfig: Record<OrderStatus, OrderStatusConfigObject> = {
  QUOTATION: {
    textColor: COLOR_ORDER_STATUS_QUOTATION,
    displayText: "Quotation",
  },
  SHIPMENT: {
    textColor: COLOR_ORDER_STATUS_SHIPMENT,
    displayText: "Shipment",
  },
  CANCELLED: {
    textColor: COLOR_ORDER_STATUS_CANCELLED,
    displayText: "Cancelled",
  },
  RETURNED: {
    textColor: COLOR_ORDER_STATUS_RETURNED,
    displayText: "Returned",
  },
}

type OrderPaymentStatusConfigObject = {
  textColor: string
  displayText: string
  needPayment: boolean
}

export const orderPaymentStatusConfig: Record<
  OrderPaymentStatus,
  OrderPaymentStatusConfigObject
> = {
  NONE: {
    textColor: COLOR_ORDER_PAYMENT_STATUS_NONE,
    displayText: "None",
    needPayment: true,
  },
  PARTIAL: {
    textColor: COLOR_ORDER_PAYMENT_STATUS_PARTIAL,
    displayText: "Partial",
    needPayment: true,
  },
  SETTLEMENT: {
    textColor: COLOR_ORDER_PAYMENT_STATUS_SETTLEMENT,
    displayText: "Settlement",
    needPayment: false,
  },
  OVERPAYMENT: {
    textColor: COLOR_ORDER_PAYMENT_STATUS_OVER_PAYMENT,
    displayText: "Overpayment",
    needPayment: false,
  },
  REFUNDED: {
    textColor: COLOR_ORDER_PAYMENT_STATUS_OVER_REFUNDED,
    displayText: "Refunded",
    needPayment: false,
  },
  DOWN_PAYMENT: {
    textColor: COLOR_ORDER_PAYMENT_STATUS_DOWN_PAYMENT,
    displayText: "Down Payment",
    needPayment: true,
  },
}

type OrderApprovalStatusConfigObject = {
  bg: string
  textColor: string
  displayText: string
}

export const orderApprovalStatusConfig: Record<
  OrderApprovalStatus,
  OrderApprovalStatusConfigObject
> = {
  NOT_REQUIRED: {
    bg: COLOR_STATUS_CLOSED_BG,
    textColor: COLOR_STATUS_CLOSED_TEXT,
    displayText: "Not Required",
  },
  WAITING_APPROVAL: {
    bg: COLOR_STATUS_RED_BG,
    textColor: COLOR_STATUS_RED_TEXT,
    displayText: "Waiting Approval",
  },
  APPROVED: {
    bg: COLOR_STATUS_GREEN_BG,
    textColor: COLOR_STATUS_GREEN_TEXT,
    displayText: "Approved",
  },
}
