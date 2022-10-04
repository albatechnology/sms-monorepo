import { TargetType } from "api/generated/enums"

import { ActivityStatus } from "./../api/generated/enums"

export type EntryStackParamList = {
  Login: undefined
  Main: undefined
  Dashboard: {
    startDate: Date
    endDate: Date
    filter: number | null
    channel: number | null
    sales: number | null
  }
  Notification: undefined
  Cart: undefined
  Checkout: { leadId: number }
  AddAddress: { customerId: number }
  UserSelectChannel: undefined
  DiscountApproval: undefined
  ReportDrillDown: {
    originalSerializedFilter: string
    filterType: TargetType
    supervisorTypeId: number | null
    parentSupervisorId: number | null
    companyId: number | null
  }
  ActivityList: {
    isDeals: boolean | null
    filterStatus: ActivityStatus | null
    filterTargetId: number | null
  }
  TableRevenue: {
    isDeals: boolean | null
    filterStatus: ActivityStatus | null
    filterTargetId: number | null
  }
}
