import { Asset } from "expo-asset"
import * as FileSystem from "expo-file-system"

import { dataURItoBlob } from "./blob"

export const formDataIncludePicture = (
  formData: FormData,
  imageUrl: string,
) => {
  if (imageUrl.includes(";base64,")) {
    formData.append("image", dataURItoBlob(imageUrl))
  } else {
    formData.append("image", {
      // @ts-ignore
      uri: imageUrl,
      name: `photo.png`,
      type: `image/png`,
    })
  }
}

export const loadLocalImageToBase64 = async (requiredAsset: any) => {
  const [{ localUri }] = await Asset.loadAsync(requiredAsset)
  const logoData = await FileSystem.readAsStringAsync(localUri, {
    encoding: FileSystem.EncodingType.Base64,
  })
  const logoImageData = "data:image/png;base64," + logoData

  return logoImageData
}
