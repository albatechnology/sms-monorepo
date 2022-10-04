import { ActivityFollowUpMethod, ActivityStatus } from "api/generated/enums"
import { V1Api } from "api/openapi"

import {
  COLOR_ACTIVITY_COLD_BG,
  COLOR_ACTIVITY_COLD_TEXT,
  COLOR_ACTIVITY_WARM_BG,
  COLOR_ACTIVITY_WARM_TEXT,
  COLOR_ACTIVITY_HOT_BG,
  COLOR_ACTIVITY_HOT_TEXT,
  COLOR_ACTIVITY_CLOSED_BG,
  COLOR_ACTIVITY_CLOSED_TEXT,
} from "helper/theme"

import { Brand } from "types/POS/ProductCategorization/Brand"

import { ActivityComment, mapActivityComment } from "./ActivityComment"
import { Channel, mapChannel } from "./Channel"
import { Customer, mapCustomer } from "./Customer"
import { Lead, mapLead } from "./Lead"
import { mapOrder, Order } from "./Order"
import { mapBrand } from "./POS/ProductCategorization/Brand"
import { mapUser, User } from "./User"
import { UnwrapOpenAPIResponse } from "./helper"

export type Activity = {
  id: number
  order: Nullable<Order>
  followUpDatetime: Date
  feedback: Nullable<string>
  followUpMethod: ActivityFollowUpMethod
  status: ActivityStatus
  channel: Nullable<Channel>
  lead: Nullable<Lead>
  user: Nullable<User>
  customer: Nullable<Customer>
  reminderDateTime: Nullable<Date>
  reminderNote: string
  latestComment: ActivityComment
  activityCommentCount: number
  brands: Brand[]
  activityBrandValues: any
  estimatedValue: []
  updatedAt: Date
  createdAt: Date
}

export type ActivityCoreData = Pick<
  Activity,
  | "followUpMethod"
  | "status"
  | "feedback"
  | "reminderDateTime"
  | "reminderNote"
  | "estimatedValue"
>

export const mapActivity = (
  apiObj: UnwrapOpenAPIResponse<V1Api["activityShow"]>,
): Activity => {
  return {
    id: apiObj.id,
    order: apiObj.order ? mapOrder(apiObj.order) : null,
    followUpDatetime: new Date(apiObj.follow_up_datetime),
    feedback: apiObj.feedback,
    followUpMethod: apiObj.follow_up_method,
    status: apiObj.status,
    channel: apiObj.channel ? mapChannel(apiObj.channel) : null,
    lead: apiObj.lead ? mapLead(apiObj.lead) : null,
    user: apiObj.user ? mapUser(apiObj.user) : null,
    customer: apiObj.customer ? mapCustomer(apiObj.customer) : null,
    reminderDateTime: apiObj.reminder_datetime
      ? new Date(apiObj.reminder_datetime)
      : null,
    reminderNote: apiObj.reminder_note,
    latestComment: apiObj.latest_comment
      ? mapActivityComment(apiObj.latest_comment)
      : null,
    activityCommentCount: apiObj.activity_comment_count,
    brands: apiObj.brands ? apiObj.brands.map(mapBrand) : null,
    estimatedValue: apiObj.estimated_value,
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
    activityBrandValues: apiObj.activity_brand_values,
  }
}

type ActivityStatusConfigObject = {
  bg: string
  textColor: string
  displayText: string
  order: number
}

export const activityStatusConfig: Record<
  ActivityStatus,
  ActivityStatusConfigObject
> = {
  HOT: {
    bg: "#E53935",
    textColor: COLOR_ACTIVITY_HOT_TEXT,
    displayText: "Hot",
    order: 1,
  },
  WARM: {
    bg: "#FFD13D",
    textColor: COLOR_ACTIVITY_WARM_TEXT,
    displayText: "Warm",
    order: 2,
  },
  COLD: {
    bg: "#0553B7",
    textColor: COLOR_ACTIVITY_COLD_TEXT,
    displayText: "Cold",
    order: 3,
  },
  CLOSED: {
    bg: "#C4C4C4",
    textColor: COLOR_ACTIVITY_CLOSED_TEXT,
    displayText: "Closed",
    order: 4,
  },
}
