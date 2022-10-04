import { NavigatorScreenParams } from "@react-navigation/native"

import { LeadType } from "api/generated/enums"

export type MainTabParamList = {
  Dashboard: NavigatorScreenParams<DashboardStackParamList>
  Product: NavigatorScreenParams<ProductStackParamList>
  Promo: NavigatorScreenParams<PromoStackParamList>
  Chat: NavigatorScreenParams<ChatStackParamList>
  Customer: undefined
}

export type DashboardStackParamList = {
  Dashboard: undefined
  SalesActivity: undefined
  ActivityDetail: { id: number; isDeals?: boolean }
}

export type ProductStackParamList = {
  Product: undefined
  Cafe: undefined
  StockSelectChannel: undefined
  Stock: { channelId: number }
  ProductDetail: { id: number }
  ProductByBrand: { id: number; brandName: string }
  ProductSearch: undefined
  ProductUnitSearch: undefined
}

export type PromoStackParamList = {
  Promo: undefined
  PromoDetail: { id: number }
  ProductDetail: { id: number }
}

export type ChatStackParamList = {
  AddChat: undefined
  Chat: undefined
  ChatDetail: { id: number }
}

export type CustomerStackParamList = {
  ActivityDetail: { id: number; isDeals?: boolean }
  AddActivity: { customerId: number; leadId: number }
  AddAddress: { customerId: number }
  AddCustomer: undefined
  AddLead: { customerId?: number; type?: LeadType; isUnhandled: boolean }
  AddLeadWithCustomer: { type?: LeadType; isUnhandled: boolean }
  CustomerDetail: { leadId: number }
  CustomerList: undefined
  EditActivity: { id: number }
  EditAddress: { addressId: number }
  EditCustomer: { id: number }
  EditLead: { id: number }
  Payment: { orderId: number }
  PaymentPayCategorySelection: { orderId: number }
  PaymentPayTypeSelection: { orderId: number; paymentCategoryId: number }
  PaymentPayConfirm: {
    orderId: number
    paymentTypeId: number
  }
  OrderPaymentInfo: {
    orderId: number
    companyId: number
  }
  OrderPaymentProof: {
    paymentId: number
  }
}
