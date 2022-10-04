import { UserType } from "api/generated/enums"
import { V1Api } from "api/openapi"

import { Company, mapCompany } from "types/Company"

import { UnwrapOpenAPIResponse } from "./helper"

export type User = {
  id: number
  name: string
  email: string
  emailVerifiedAt: Date
  type: UserType
  company: Company
  companyId: Nullable<number>
  channelId: Nullable<number>
  supervisorId: Nullable<number>
  supervisorTypeId: Nullable<number>
  initial: string
  reportable_type: string
  as: string
  discount_approval_limit_percentage: Nullable<number>
  app_show_hpp: boolean
  app_approve_discount: boolean
  app_create_lead: boolean
}

export const mapUser = (
  apiObj: UnwrapOpenAPIResponse<V1Api["userShow"]>,
): User => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    email: apiObj.email,
    emailVerifiedAt: new Date(apiObj.email_verified_at),
    type: apiObj.type,
    company: apiObj.company ? mapCompany(apiObj.company) : null,
    companyId: apiObj.company_id,
    channelId: apiObj.channel_id,
    supervisorId: apiObj.supervisor_id,
    supervisorTypeId: apiObj.supervisor_type_id,
    initial: apiObj.initial,
    reportable_type: apiObj.reportable_type,
    as: apiObj.as,
    discount_approval_limit_percentage:
      apiObj.discount_approval_limit_percentage,
    app_show_hpp: apiObj.app_show_hpp,
    app_create_lead: apiObj.app_create_lead,
    app_approve_discount: apiObj.app_approve_discount,
  }
}
