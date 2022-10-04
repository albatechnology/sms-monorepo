import { useAppState } from "@react-native-community/hooks"
import * as Updates from "expo-updates"
import React, { useEffect, useState } from "react"
import { View, Text, TouchableHighlight, StyleSheet } from "react-native"

export default () => {
  const currentAppState = useAppState()

  const [showMessage, setShowMessage] = useState(false)

  useEffect(() => {
    ;(async () => {
      try {
        const { isAvailable } = await Updates.checkForUpdateAsync()
        if (isAvailable) {
          const { isNew } = await Updates.fetchUpdateAsync()
          if (isNew) {
            setShowMessage(true)
            Updates.reloadAsync()
          }
        }
      } catch (e) {}
    })()
  }, [currentAppState])

  if (Updates.isEmergencyLaunch) {
    Updates.reloadAsync()
  }

  if (!showMessage) {
    return null
  }

  return (
    <View style={styles.container}>
      <Text style={styles.text}>Update available. Auto updating...</Text>
      <TouchableHighlight>
        <Text
          style={styles.text}
          onPress={() => {
            Updates.reloadAsync()
          }}
        >
          Update now
        </Text>
      </TouchableHighlight>
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    position: "absolute",
    bottom: 0,
    left: 0,
    right: 0,
    padding: 20,
    display: "flex",
    flexDirection: "row",
    justifyContent: "space-between",
    backgroundColor: "black",
  },
  text: {
    color: "white",
    fontFamily: "FontBold",
  },
})
