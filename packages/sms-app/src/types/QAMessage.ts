import { V1Api } from "api/openapi"

import { mapQATopic, QATopicSummary } from "./QATopic"
import { mapUser, User } from "./User"
import { UnwrapOpenAPIResponse } from "./helper"

export type QAMessage = {
  id: number
  topic: QATopicSummary
  sender: User
  content: string
}

export const mapQAMessage = (
  apiObj: UnwrapOpenAPIResponse<V1Api["qaMessageShow"]>,
): QAMessage => {
  return {
    id: apiObj.id,
    topic: apiObj.topic,
    sender: mapUser(apiObj.sender),
    content: apiObj.content,
  }
}
