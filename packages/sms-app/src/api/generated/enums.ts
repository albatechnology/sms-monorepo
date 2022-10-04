export const ActivityFollowUpMethodList = ["PHONE","WHATSAPP","MEETING","OTHERS","WALK_IN_CUSTOMER","NEW_ORDER"]
export type ActivityFollowUpMethod = "PHONE" | "WHATSAPP" | "MEETING" | "OTHERS" | "WALK_IN_CUSTOMER" | "NEW_ORDER"
export const ActivityFollowUpMethodReadOnlyList = ["NEW_ORDER"]

export const ActivityStatusList = ["HOT","WARM","COLD","CLOSED"]
export type ActivityStatus = "HOT" | "WARM" | "COLD" | "CLOSED"
export const ActivityStatusReadOnlyList = []

export const AddressTypeList = ["ADDRESS","DELIVERY","BILLING"]
export type AddressType = "ADDRESS" | "DELIVERY" | "BILLING"
export const AddressTypeReadOnlyList = []

export const CacheKeyList = ["ALL_COMPANIES_COLLECTION","ALL_CHANNELS_COLLECTION","ALL_SUPERVISOR_TYPES_COLLECTION"]
export type CacheKey = "ALL_COMPANIES_COLLECTION" | "ALL_CHANNELS_COLLECTION" | "ALL_SUPERVISOR_TYPES_COLLECTION"
export const CacheKeyReadOnlyList = []

export const CacheTagsList = ["COMPANY","CHANNEL","SUPERVISOR_TYPE"]
export type CacheTags = "COMPANY" | "CHANNEL" | "SUPERVISOR_TYPE"
export const CacheTagsReadOnlyList = []

export const DiscountableTypeList = ["ORDER","PRODUCT"]
export type DiscountableType = "ORDER" | "PRODUCT"
export const DiscountableTypeReadOnlyList = []

export const DiscountErrorList = ["INACTIVE","USE_LIMIT_REACHED","UNDER_MINIMUM_PRICE","NOT_APPLICABLE_TO_ANY_PRODUCT"]
export type DiscountError = "INACTIVE" | "USE_LIMIT_REACHED" | "UNDER_MINIMUM_PRICE" | "NOT_APPLICABLE_TO_ANY_PRODUCT"
export const DiscountErrorReadOnlyList = []

export const DiscountScopeList = ["QUANTITY","TYPE","TRANSACTION"]
export type DiscountScope = "QUANTITY" | "TYPE" | "TRANSACTION"
export const DiscountScopeReadOnlyList = []

export const DiscountTypeList = ["NOMINAL","PERCENTAGE"]
export type DiscountType = "NOMINAL" | "PERCENTAGE"
export const DiscountTypeReadOnlyList = []

export const ExportModelList = ["PRODUCT","PRODUCT_BRAND","PRODUCT_MODEL","PRODUCT_VERSION","PRODUCT_CATEGORY_CODE","PRODUCT_UNIT","COLOUR","COVERING"]
export type ExportModel = "PRODUCT" | "PRODUCT_BRAND" | "PRODUCT_MODEL" | "PRODUCT_VERSION" | "PRODUCT_CATEGORY_CODE" | "PRODUCT_UNIT" | "COLOUR" | "COVERING"
export const ExportModelReadOnlyList = []

export const LeadStatusList = ["GREEN","YELLOW","RED","EXPIRED","SALES","OTHER_SALES"]
export type LeadStatus = "GREEN" | "YELLOW" | "RED" | "EXPIRED" | "SALES" | "OTHER_SALES"
export const LeadStatusReadOnlyList = []

export const LeadTypeList = ["PROSPECT","CLOSED","LEADS"]
export type LeadType = "PROSPECT" | "CLOSED" | "LEADS"
export const LeadTypeReadOnlyList = []

export const NotificationTypeList = ["ActivityReminder"]
export type NotificationType = "ActivityReminder"
export const NotificationTypeReadOnlyList = []

export const OrderApprovalStatusList = ["NOT_REQUIRED","WAITING_APPROVAL","APPROVED"]
export type OrderApprovalStatus = "NOT_REQUIRED" | "WAITING_APPROVAL" | "APPROVED"
export const OrderApprovalStatusReadOnlyList = []

export const OrderDetailShipmentStatusList = ["NONE","PARTIAL","PREPARING","DELIVERING","ARRIVED"]
export type OrderDetailShipmentStatus = "NONE" | "PARTIAL" | "PREPARING" | "DELIVERING" | "ARRIVED"
export const OrderDetailShipmentStatusReadOnlyList = []

export const OrderDetailStatusList = ["NOT_FULFILLED","PARTIALLY_FULFILLED","FULFILLED","OVER_FULFILLED"]
export type OrderDetailStatus = "NOT_FULFILLED" | "PARTIALLY_FULFILLED" | "FULFILLED" | "OVER_FULFILLED"
export const OrderDetailStatusReadOnlyList = []

export const OrderPaymentStatusList = ["NONE","PARTIAL","SETTLEMENT","OVERPAYMENT","REFUNDED","DOWN_PAYMENT"]
export type OrderPaymentStatus = "NONE" | "PARTIAL" | "SETTLEMENT" | "OVERPAYMENT" | "REFUNDED" | "DOWN_PAYMENT"
export const OrderPaymentStatusReadOnlyList = []

export const OrderShipmentStatusList = ["NONE","PARTIAL","PREPARING","DELIVERING","ARRIVED"]
export type OrderShipmentStatus = "NONE" | "PARTIAL" | "PREPARING" | "DELIVERING" | "ARRIVED"
export const OrderShipmentStatusReadOnlyList = []

export const OrderStatusList = ["QUOTATION","SHIPMENT","CANCELLED","RETURNED"]
export type OrderStatus = "QUOTATION" | "SHIPMENT" | "CANCELLED" | "RETURNED"
export const OrderStatusReadOnlyList = []

export const OrderStockStatusList = ["INDENT","FULFILLED"]
export type OrderStockStatus = "INDENT" | "FULFILLED"
export const OrderStockStatusReadOnlyList = []

export const PaymentStatusList = ["PENDING","APPROVED","REJECTED"]
export type PaymentStatus = "PENDING" | "APPROVED" | "REJECTED"
export const PaymentStatusReadOnlyList = []

export const PersonTitleList = ["MR","MS","MRS"]
export type PersonTitle = "MR" | "MS" | "MRS"
export const PersonTitleReadOnlyList = []

export const ProductCategoryTypeList = ["COLLECTION","SUB_COLLECTION","BRAND_TYPE","BRAND","CATEGORY"]
export type ProductCategoryType = "COLLECTION" | "SUB_COLLECTION" | "BRAND_TYPE" | "BRAND" | "CATEGORY"
export const ProductCategoryTypeReadOnlyList = []

export const ReportableTypeList = ["COMPANY","CHANNEL","USER"]
export type ReportableType = "COMPANY" | "CHANNEL" | "USER"
export const ReportableTypeReadOnlyList = []

export const ReportPipelineModeList = ["ADD_TARGET_MAP","EVALUATE_TARGET","ADD_NEW_TARGET_MAP_TO_TARGET"]
export type ReportPipelineMode = "ADD_TARGET_MAP" | "EVALUATE_TARGET" | "ADD_NEW_TARGET_MAP_TO_TARGET"
export const ReportPipelineModeReadOnlyList = []

export const ShipmentStatusList = ["PREPARING","DELIVERING","ARRIVED","CANCELLED"]
export type ShipmentStatus = "PREPARING" | "DELIVERING" | "ARRIVED" | "CANCELLED"
export const ShipmentStatusReadOnlyList = []

export const StockHistoryDirectionList = ["OUTBOUND","INBOUND"]
export type StockHistoryDirection = "OUTBOUND" | "INBOUND"
export const StockHistoryDirectionReadOnlyList = []

export const StockHistoryTypeList = ["MANUAL","ORDER","TRANSFER"]
export type StockHistoryType = "MANUAL" | "ORDER" | "TRANSFER"
export const StockHistoryTypeReadOnlyList = []

export const StockTransferStatusList = ["PENDING","FAILED","COMPLETE"]
export type StockTransferStatus = "PENDING" | "FAILED" | "COMPLETE"
export const StockTransferStatusReadOnlyList = []

export const TargetBreakdownTypeList = ["ACTIVITY_STATUS"]
export type TargetBreakdownType = "ACTIVITY_STATUS"
export const TargetBreakdownTypeReadOnlyList = []

export const TargetChartTypeList = ["SINGLE","MULTIPLE"]
export type TargetChartType = "SINGLE" | "MULTIPLE"
export const TargetChartTypeReadOnlyList = []

export const TargetTypeList = ["DEALS_INVOICE_PRICE","DEALS_PAYMENT_PRICE","DEALS_BRAND_PRICE","DEALS_MODEL_PRICE","DEALS_ORDER_COUNT","DEALS_BRAND_COUNT","DEALS_MODEL_COUNT","ACTIVITY_COUNT","ACTIVITY_COUNT_CLOSED","ORDER_SETTLEMENT_COUNT"]
export type TargetType = "DEALS_INVOICE_PRICE" | "DEALS_PAYMENT_PRICE" | "DEALS_BRAND_PRICE" | "DEALS_MODEL_PRICE" | "DEALS_ORDER_COUNT" | "DEALS_BRAND_COUNT" | "DEALS_MODEL_COUNT" | "ACTIVITY_COUNT" | "ACTIVITY_COUNT_CLOSED" | "ORDER_SETTLEMENT_COUNT"
export const TargetTypeReadOnlyList = []

export const UserTypeList = ["DEFAULT","SALES","SUPERVISOR","DIRECTOR"]
export type UserType = "DEFAULT" | "SALES" | "SUPERVISOR" | "DIRECTOR"
export const UserTypeReadOnlyList = []