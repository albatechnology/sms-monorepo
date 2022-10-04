import React from "react"
import {
  Text,
  View,
  StyleSheet,
  RefreshControl,
  ScrollView,
} from "react-native"

import ErrorSVG from "../svg/error"

type Props = {
  refreshing: boolean
  onRefresh: () => void
}

export default function Error({
  refreshing = false,
  onRefresh = () => {},
}: Props) {
  return (
    <ScrollView
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      <View style={styles.container}>
        <ErrorSVG />
        <Text style={styles.text}>
          Maaf, terjadi kesalahan melakukan aksi ini.
        </Text>
      </View>
    </ScrollView>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    marginVertical: 40,
    alignItems: "center",
  },
  text: {
    marginTop: 20,
  },
})
