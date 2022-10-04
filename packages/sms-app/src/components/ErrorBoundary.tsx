import React from "react"
import { View, Text, StyleSheet } from "react-native"
import ErrorBoundary from "react-native-error-boundary"
import { Button } from "react-native-magnus"

import { logError } from "api/errors"
import { ErrorType } from "api/errors/errorType"

import { queryClient } from "../query"
import ErrorSVG from "../svg/error"

const errorHandler = (error, stackTrace) => {
  console.error(error)
  logError(error, ErrorType.NATIVE, stackTrace)
}

export default (props) => {
  return (
    <ErrorBoundary
      onError={errorHandler}
      FallbackComponent={({ error, resetError }) => (
        <View style={styles.container}>
          <ErrorSVG />
          <Text style={styles.text}>
            Error: Maaf, kami akan perbaiki masalah ini secepat mungkin.
          </Text>
          <Button
            color="white"
            underlayColor="black"
            bg="black"
            w="100%"
            mt={3}
            onPress={() => {
              queryClient.clear()
              resetError()
            }}
          >
            Refresh
          </Button>
        </View>
      )}
    >
      {props.children}
    </ErrorBoundary>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    marginTop: 200,
    marginBottom: 40,
    marginLeft: 10,
    marginRight: 10,
    alignItems: "center",
  },
  text: {
    marginTop: 20,
  },
})
