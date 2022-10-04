import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type ReportTargetLine = {
  id: number
  targetId: number
  label: string
  target: number
  value: number
  updatedAt: Date
  createdAt: Date
}

export const mapReportTargetLine = (
  apiObj: UnwrapOpenAPIResponse<
    V1Api["targetIndex"]
  >[number]["target_lines"][number],
): ReportTargetLine => {
  return {
    id: apiObj.id,
    targetId: apiObj.target_id,
    label: apiObj.label,
    target: apiObj.target,
    value: apiObj.value,
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
  }
}
