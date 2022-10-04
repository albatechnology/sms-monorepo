import { LeadStatus, LeadType } from "api/generated/enums"
import { V1Api } from "api/openapi"

import {
  COLOR_STATUS_RED_BG,
  COLOR_STATUS_RED_TEXT,
  COLOR_STATUS_YELLOW_BG,
  COLOR_STATUS_GREEN_BG,
  COLOR_STATUS_GREEN_TEXT,
  COLOR_STATUS_CLOSED_BG,
  COLOR_STATUS_CLOSED_TEXT,
  COLOR_STATUS_YELLOW_TEXT,
} from "helper/theme"

import { mapActivity } from "types/Activity"
import { Channel, mapChannel } from "types/Channel"
import { Customer, mapCustomer } from "types/Customer"
import { User, mapUser } from "types/User"

import { Activity } from "./Activity"
import { LeadCategory, mapLeadCategory } from "./LeadCategory"
import { UnwrapOpenAPIResponse } from "./helper"

export type Lead = {
  id: number
  type: LeadType
  status: LeadStatus
  label: Nullable<string>
  latestActivity: Activity
  user: User
  customer: Customer
  channel: Channel
  leadCategory: LeadCategory
  // So we can get the data on edit form
  leadCategoryId: number
  isUnhandled: boolean
  hasActivity: boolean
  updatedAt: Date
  createdAt: Date
  leadSubCategory?: object
  interest: Nullable<string>
}

export const mapLead = (
  apiObj: UnwrapOpenAPIResponse<V1Api["leadShow"]>,
): Lead => {
  return {
    id: apiObj.id,
    type: apiObj.type,
    status: apiObj.status,
    label: apiObj.label,
    leadCategory: apiObj.lead_category
      ? mapLeadCategory(apiObj.lead_category)
      : null,
    leadSubCategory: apiObj.lead_sub_category,
    leadCategoryId: apiObj.lead_category
      ? mapLeadCategory(apiObj.lead_category).id
      : null,
    latestActivity: !!apiObj.latest_activity
      ? mapActivity(apiObj.latest_activity)
      : null,
    customer: !!apiObj.customer ? mapCustomer(apiObj.customer) : null,
    user: !!apiObj.user ? mapUser(apiObj.user) : null,
    channel: !!apiObj.channel ? mapChannel(apiObj.channel) : null,
    isUnhandled: apiObj.is_unhandled,
    hasActivity: apiObj.has_activity,
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
    interest: apiObj.interest,
  }
}

type LeadStatusConfigObject = {
  bg: string
  textColor: string
  displayText: string
}

export const leadStatusConfig: Record<LeadStatus, LeadStatusConfigObject> = {
  RED: {
    bg: COLOR_STATUS_RED_BG,
    textColor: COLOR_STATUS_RED_TEXT,
    displayText: "Red",
  },
  YELLOW: {
    bg: COLOR_STATUS_YELLOW_BG,
    textColor: COLOR_STATUS_YELLOW_TEXT,
    displayText: "Yellow",
  },
  GREEN: {
    bg: COLOR_STATUS_GREEN_BG,
    textColor: COLOR_STATUS_GREEN_TEXT,
    displayText: "Green",
  },
  EXPIRED: {
    bg: COLOR_STATUS_CLOSED_BG,
    textColor: COLOR_STATUS_CLOSED_TEXT,
    displayText: "Expired",
  },
  SALES: {
    bg: COLOR_STATUS_CLOSED_BG,
    textColor: COLOR_STATUS_CLOSED_TEXT,
    displayText: "Sales",
  },
  OTHER_SALES: {
    bg: COLOR_STATUS_CLOSED_BG,
    textColor: COLOR_STATUS_CLOSED_TEXT,
    displayText: "Other Sales",
  },
}
