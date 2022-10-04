import NetInfo from "@react-native-community/netinfo"
import React, { useEffect, useState } from "react"
import Spinner from "react-native-loading-spinner-overlay"

export default () => {
  const [showMessage, setShowMessage] = useState(false)

  useEffect(() => {
    const unsubscribe = NetInfo.addEventListener((state) => {
      setShowMessage(!state.isConnected)
    })

    return () => {
      unsubscribe()
    }
  })

  if (!showMessage) {
    return null
  }

  return (
    <Spinner
      visible={showMessage}
      textContent="Tidak terkoneksi ke internet. Mohon koneksikan ke internet kembali sebelum mencoba lagi."
      textStyle={{
        textAlign: "center",
        margin: 5,
        color: "#FFF",
      }}
    />
  )
}
