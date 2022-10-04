import { TargetType, TargetChartType } from "api/generated/enums"
import { V1Api } from "api/openapi"

import { formatCurrency } from "helper"
import { COLOR_GREEN, COLOR_RED, COLOR_YELLOW } from "helper/theme"

import { Report, mapReport } from "types/Report"
import { ReportTargetLine, mapReportTargetLine } from "types/ReportTargetLine"

import {
  mapReportTargetBreakdown,
  ReportTargetBreakdown,
} from "./ReportTargetBreakdown"
import { User, mapUser } from "./User"
import { UnwrapOpenAPIResponse } from "./helper"

export type ReportTarget = {
  id: number
  reportId: number
  type: TargetType
  chartType: TargetChartType
  target: number
  value: number
  updatedAt: Date
  createdAt: Date
  report: Report
  user: User
  targetLines: ReportTargetLine[]
  breakdown: ReportTargetBreakdown[]
}

export const mapReportTarget = (
  apiObj: UnwrapOpenAPIResponse<V1Api["targetIndex"]>[number],
): ReportTarget => {
  return {
    id: apiObj.id,
    reportId: apiObj.report_id,
    type: apiObj.type,
    chartType: apiObj.chart_type,
    target: apiObj.target,
    value: apiObj.value,
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
    report: mapReport(apiObj.report),
    user: apiObj.user ? mapUser(apiObj.user) : null,
    targetLines: apiObj.target_lines?.map(mapReportTargetLine),
    breakdown: apiObj.breakdown
      ? apiObj.breakdown.map(mapReportTargetBreakdown)
      : null,
  }
}

type TargetTypeConfigObject = {
  displayText: string
  formatValue: (val: any) => any
}

const returnSelf = (x) => x

export const targetTypeConfig: Record<TargetType, TargetTypeConfigObject> = {
  DEALS_INVOICE_PRICE: {
    displayText: "Sales Revenue",
    formatValue: formatCurrency,
  },
  DEALS_PAYMENT_PRICE: {
    displayText: "Realisasi Pembayaran",
    formatValue: formatCurrency,
  },
  DEALS_BRAND_PRICE: {
    displayText: "Total Transaksi by Brand",
    formatValue: formatCurrency,
  },
  DEALS_MODEL_PRICE: {
    displayText: "Total Transaksi by Model",
    formatValue: formatCurrency,
  },
  DEALS_ORDER_COUNT: {
    displayText: "Jumlah Sales Order",
    formatValue: returnSelf,
  },
  DEALS_BRAND_COUNT: {
    displayText: "Jumlah Transaksi by Brand",
    formatValue: returnSelf,
  },
  DEALS_MODEL_COUNT: {
    displayText: "Jumlah Transaksi by Model",
    formatValue: returnSelf,
  },
  ACTIVITY_COUNT: { displayText: "Jumlah Follow Up", formatValue: returnSelf },
  ACTIVITY_COUNT_CLOSED: {
    displayText: "Lead Expired",
    formatValue: returnSelf,
  },
  ORDER_SETTLEMENT_COUNT: {
    displayText: "Jumlah Pelunasan Sales Order x Jumlah Sales Order",
    formatValue: returnSelf,
  },
}

export const calculateProgressBarColor = (percentage: number) => {
  if (percentage < 33) {
    return COLOR_RED
  } else if (percentage < 66) {
    return COLOR_YELLOW
  } else {
    return COLOR_GREEN
  }
}

export const processReportTarget = (rawReportTargets: ReportTarget[]) => {
  if (!rawReportTargets) {
    return []
  }

  // Hardcode: For ORDER_SETTLEMENT_COUNT, the target should be the value of DEALS_ORDER_COUNT
  const dealsOrderCountTargets = rawReportTargets.filter(
    (reportTarget) => reportTarget.type === "DEALS_ORDER_COUNT",
  )
  // Hardcode: For DEALS_PAYMENT_PRICE, the target should be the value of DEALS_INVOICE_PRICE
  const dealsInvoicePriceTargets = rawReportTargets.filter(
    (reportTarget) => reportTarget.type === "DEALS_INVOICE_PRICE",
  )
  // This is the base that we'll use and modify the value (we don't want DEALS_ORDER_COUNT to show on a different panel, the other one does though)
  const allOtherTarget = rawReportTargets.filter(
    (reportTarget) => reportTarget.type !== "DEALS_ORDER_COUNT",
  )
  const finalReportTargets = allOtherTarget.map((reportTarget) => {
    if (reportTarget.type === "ORDER_SETTLEMENT_COUNT") {
      return {
        ...reportTarget,
        // Replace with the new value
        target: dealsOrderCountTargets.find(
          (r) => r.reportId === reportTarget.reportId,
        ).value,
      }
    }

    if (reportTarget.type === "DEALS_PAYMENT_PRICE") {
      return {
        ...reportTarget,
        // Replace with the new value
        target: dealsInvoicePriceTargets.find(
          (r) => r.reportId === reportTarget.reportId,
        ).value,
      }
    }

    return reportTarget
  })

  return finalReportTargets
}

export const handleTargetDrilldown = (
  navigation: any,
  computedFilters: any,
  reportTarget: ReportTarget,
  inferredCompanyId?: number,
) => {
  // Nothing to drill down to if channel
  if (reportTarget.report.reportableType === "CHANNEL") {
    return
  }

  let companyId = inferredCompanyId
  let supervisorTypeId
  // Set to null if company
  if (reportTarget.report.reportableType === "COMPANY") {
    supervisorTypeId = null
    companyId = reportTarget.report.reportableId
  } else {
    // If it's user
    if (!!reportTarget.user) {
      // And it's a supervisor, then we can just pass it
      if (!!reportTarget.user.supervisorTypeId) {
        supervisorTypeId = reportTarget.user.supervisorTypeId
      } else {
        // Otherwise it's sales, then we also don't do anything
        return
      }
    } else {
      throw new Error("User object not found")
    }
  }

  navigation.push("ReportDrillDown", {
    originalSerializedFilter: JSON.stringify(computedFilters),
    filterType: reportTarget.type,
    supervisorTypeId,
    parentSupervisorId: reportTarget?.user?.id ?? null,
    companyId,
  })
}
