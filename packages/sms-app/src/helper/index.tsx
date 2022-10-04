import { format, min, max } from "date-fns"
import id from "date-fns/locale/id"
// import * as MediaLibrary from "expo-media-library"
// import * as Sharing from "expo-sharing"
import { Platform } from "react-native"
import { RFValue } from "react-native-responsive-fontsize"

export const formatCurrency = (number: number, showCurrency = true) => {
  if (showCurrency) {
    const formatted = new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(number)

    if (Platform.OS !== "web") {
      return formatted.replace(/^(\D+)/, "$1 ")
    }
    return formatted
  }
  return new Intl.NumberFormat("id-ID").format(number)
}

export const responsive = (size: number) => {
  return Platform.OS === "web" ? size : RFValue(size)
}

export const formatDate = (date: Date) => {
  return format(new Date(date), "dd MMMM yyyy, HH:mm", { locale: id })
}

export const formatDateOnly = (date: Date) => {
  return format(new Date(date), "dd MMMM yyyy", { locale: id })
}

export const formatTimeOnly = (date: Date) => {
  return format(new Date(date), "HH:mm", { locale: id })
}

export const clampDate = (minDate: Date, value: Date, maxDate: Date) => {
  return min([max([value, minDate]), maxDate])
}
// export const saveImage = async (url: string) => {
//   const { status } = await MediaLibrary.requestPermissionsAsync()
//   if (status === "granted") {
//     const { uri } = await FileSystem.downloadAsync(
//       url,
//       FileSystem.cacheDirectory + "ilios.jpg",
//     )

//     return MediaLibrary.createAssetAsync(uri)
//       .then(() => {
//         toast("Berhasil disimpan")
//       })
//       .catch((e) => {
//         toast("Terjadi kesalahan, mohon coba lagi nanti.")
//       })
//   }

//   toast("Permission required.")
//   return Promise.reject()
// }

// export const shareImage = async (url: string) => {
//   const isAvailable = await Sharing.isAvailableAsync()

//   if (isAvailable) {
//     const { uri } = await FileSystem.downloadAsync(
//       url,
//       FileSystem.cacheDirectory + "ilios.jpg",
//     )
//     await Sharing.shareAsync(uri)
//   } else {
//     toast("Sharing tidak tersedia di platform anda.")
//   }
// }

export const hexColorFromString = (str: string) => {
  let hash = 0

  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash)
  }

  let c = (hash & 0x00ffffff).toString(16).toUpperCase()

  return "00000".substring(0, 6 - c.length) + c
}
