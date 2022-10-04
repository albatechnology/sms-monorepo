import { V1Api } from "api/openapi"

import { ImageType, mapImages } from "types/Image"

import { mapQAMessage, QAMessage } from "./QAMessage"
import { mapUser, User } from "./User"
import { UnwrapOpenAPIResponse } from "./helper"

export type QATopic = {
  id: number
  subject: string
  creator: User
  latestMessage: QAMessage
  images: Nullable<ImageType[]>
  updatedAt: Date
  createdAt: Date
}

export type QATopicSummary = Pick<QATopic, "id" | "subject">

export const mapQATopic = (
  apiObj: UnwrapOpenAPIResponse<V1Api["qaTopicIndex"]>[number],
): QATopic => {
  return {
    id: apiObj.id,
    subject: apiObj.subject,
    creator: mapUser(apiObj.creator),
    latestMessage: !!apiObj.latest_message
      ? mapQAMessage(apiObj.latest_message)
      : null,
    images: apiObj?.images?.length > 0 ? mapImages(apiObj.images) : null,
    updatedAt: new Date(apiObj.updated_at),
    createdAt: new Date(apiObj.created_at),
  }
}
