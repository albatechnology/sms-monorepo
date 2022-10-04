import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type ReportTargetBreakdown = {
  enumType: string
  enumValue: string
  value: number
}

export const mapReportTargetBreakdown = (
  apiObj: UnwrapOpenAPIResponse<
    V1Api["targetIndex"]
  >[number]["breakdown"][number],
): ReportTargetBreakdown => {
  return {
    enumType: apiObj.enum_type,
    enumValue: apiObj.enum_value,
    value: apiObj.value,
  }
}
