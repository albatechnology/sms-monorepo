import { V1Api } from "api/openapi"

import { mapUser, User } from "./User"
import { UnwrapOpenAPIResponse } from "./helper"

export type ActivityComment = {
  id: number
  content: string
  activityId: number
  activityCommentId: Nullable<number>
  activityCommentContent: string
  user: User
  updatedAt: Date
  createdAt: Date
}

export const mapActivityComment = (
  apiObj: UnwrapOpenAPIResponse<V1Api["activityCommentShow"]>,
): ActivityComment => {
  return {
    id: apiObj.id,
    content: apiObj.content,
    activityId: apiObj.activity_id,
    activityCommentId: apiObj.activity_comment_id,
    activityCommentContent: apiObj.activity_comment_content,
    user: !!apiObj.user ? mapUser(apiObj.user) : null,
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
  }
}
