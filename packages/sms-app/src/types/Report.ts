import { ReportableType } from "api/generated/enums"
import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type Report = {
  id: number
  name: string
  startDate: Date
  endDate: Date
  reportableLabel: string
  reportableType: ReportableType
  reportableId: number
  updatedAt: Date
  createdAt: Date
}

export const mapReport = (
  apiObj: UnwrapOpenAPIResponse<V1Api["reportShow"]>,
): Report => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    startDate: new Date(apiObj.start_date),
    endDate: new Date(apiObj.end_date),
    reportableLabel: apiObj.reportable_label,
    reportableType: apiObj.reportable_type,
    reportableId: apiObj.reportable_id,
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
  }
}
