import {
  useFonts,
  Roboto_400Regular,
  Roboto_700Bold,
} from "@expo-google-fonts/roboto"
import AppLoading from "expo-app-loading"
import "intl"
import "intl/locale-data/jsonp/id"
import React from "react"
import { LogBox, TouchableOpacity } from "react-native"
import { AppearanceProvider } from "react-native-appearance"
import { ThemeProvider } from "react-native-magnus"
import { SafeAreaProvider } from "react-native-safe-area-context"
import { enableScreens } from "react-native-screens"
import "react-native-url-polyfill/auto"
import { QueryClientProvider } from "react-query"

import ErrorBoundary from "components/ErrorBoundary"

import { AuthProvider } from "providers/Auth"
import { CartProvider } from "providers/Cart"

import { theme } from "helper/theme"

import Root from "./src/Root"
import { queryClient } from "./src/query"

enableScreens()

// Suppress timer warnings
LogBox.ignoreLogs(["Setting a timer"])

// @ts-ignore
TouchableOpacity.defaultProps = {
  // @ts-ignore
  ...TouchableOpacity.defaultProps,
  delayPressIn: 50,
  activeOpacity: 0.8,
}

export default () => {
  const [fontLoaded] = useFonts({
    FontRegular: require("./src/assets/font/Poppins-Regular.ttf"),
    FontBold: require("./src/assets/font/Poppins-Bold.ttf"),
  })

  if (!fontLoaded) {
    return <AppLoading />
  }

  return (
    <SafeAreaProvider>
      <ErrorBoundary>
        <QueryClientProvider client={queryClient}>
          <ComposeProvider providers={[AuthProvider, CartProvider]}>
            <AppearanceProvider>
              <ThemeProvider theme={theme}>
                <Root />
              </ThemeProvider>
            </AppearanceProvider>
          </ComposeProvider>
        </QueryClientProvider>
      </ErrorBoundary>
    </SafeAreaProvider>
  )
}

const ComposeProvider = ({ providers, children }) => {
  return providers.reverse().reduce((acc, Val) => <Val>{acc}</Val>, children)
}
