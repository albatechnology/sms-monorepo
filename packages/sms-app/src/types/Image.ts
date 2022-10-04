import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type ImageType = {
  id: number
  name: string
  mimeType: string
  url: string
  thumbnail: string
  preview: string
}

export const mapImage = (
  apiObj: UnwrapOpenAPIResponse<
    V1Api["productBrand"]
  >[number]["images"][number],
): ImageType => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    mimeType: apiObj.mime_type,
    url: apiObj.url,
    thumbnail: apiObj.thumbnail,
    preview: apiObj.preview,
  }
}

export const mapImages = (
  apiObj: UnwrapOpenAPIResponse<V1Api["productBrand"]>[number]["images"],
): ImageType[] => {
  return apiObj.map(mapImage)
}
