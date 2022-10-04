import axios, { AxiosError } from "axios"
import Constants from "expo-constants"
import { Platform } from "react-native"

import Languages from "helper/languages"

// import * as Sentry from "sentry-expo"
import { ErrorType, getErrorTypeDescription } from "./errorType"

const stagingWebhookUrl =
  "https://discord.com/api/webhooks/815371915866537994/OwXeJQxtLlGgMhOtfaqYc6yfOOmSFJC9-K5t9wLwT1MyzWzZcjUy_lEi9WXSEb2vpGbJ"

const prodWebhookUrl =
  "https://discord.com/api/webhooks/847161042936266852/qrGTBXlbc8R2-TEulszDESVisOAv9JEai9DZRbPSVDKTxPBYP5iVYnLQ4crNn5Dfoo_w"

const prodNativeWebhookUrl =
  "https://discord.com/api/webhooks/847161295841263656/mAs1EZ0Rj69NbSzScaVPNAzdACPCpdIBJFkfOkd9uQVaCEYT0BakGdj37NEci8kXYaNy"

const getWebhookUrl = (errorType: ErrorType) => {
  if (
    Constants?.manifest?.releaseChannel &&
    Constants?.manifest?.releaseChannel?.indexOf("prod") !== -1
  ) {
    if (errorType === ErrorType.NATIVE) {
      return prodNativeWebhookUrl
    }

    return prodWebhookUrl
  }

  // Staging
  return stagingWebhookUrl
}

export type CustomAxiosErrorType = {
  axiosError: AxiosError
  loggedIn: boolean
  logout: () => void
}

export type HandledAxiosReturn<Type = any> = {
  data: Type
}

export const discordAlert = (
  errorObj,
  errorType: ErrorType,
  stackTrace = "",
) => {
  const errorTypeDescription = getErrorTypeDescription(errorType)

  const errorString = JSON.stringify(errorObj)
  // 2048 char per message
  const messageSplitCount = Math.ceil(errorString.length / 2048)

  const messages = []
  for (let i = 0; i < messageSplitCount; i++) {
    messages.push({
      ...(i === 0 ? { title: "Message" } : {}),
      description: errorString.slice(2048 * i, 2048 * (i + 1)),
      color: 15158332,
    })
  }

  return axios.post(getWebhookUrl(errorType), {
    content: `${new Date()}  BuildID: ${Constants.manifest.revisionId} ${
      Constants.manifest.revisionId === undefined ? "(Probably Dev)" : ""
    }`,
    embeds: [
      {
        title: errorType.toString(),
        description: errorTypeDescription,
        timestamp: new Date().toISOString(),
        color: 1752220,
      },
      ...messages,
    ],
  })
}

export const logError = (
  error: any,
  errorType: ErrorType,
  stackTrace: string = "",
) => {
  if (error?.message === "Network Error") {
    return
  }
  // No need to log if we're in DEV
  if (!__DEV__) {
    discordAlert(error, errorType, stackTrace)
    if (Platform.OS !== "web") {
      // Sentry.Native.captureException(new Error(JSON.stringify(error)))
    }
  }
}

export const customErrorHandler =
  (
    statusResponseHandler?: Record<
      number,
      (error: CustomAxiosErrorType) => any
    >,
  ) =>
  (error: CustomAxiosErrorType) => {
    const errorObj = new Error()
    try {
      if (error instanceof Error) {
        throw error
      }

      const { axiosError: err, loggedIn, logout } = error
      if (err.response) {
        if (err.response.status === 401) {
          if (loggedIn) {
            toast("You have been logged out.")
            logout()
          } else {
            // If we get this while not logged in, then we're probably calling wrong function
            // Other case is on startup which we don't mind
          }
        } else if (err.response.status === 400 && err.response?.data?.message) {
          logError(err.response, ErrorType.AXIOS, errorObj.stack)
          toast(err.response?.data?.message + " (400 Error)")
        } else if (err.response.status === 422) {
          const errorMessages = Object.values(err.response?.data?.errors)
            .flat()
            .join(" ")
          logError(err.response, ErrorType.AXIOS, errorObj.stack)
          toast(errorMessages + " (422 Error)")
        } else if (
          statusResponseHandler &&
          Object.keys(statusResponseHandler).includes(
            err.response.status.toString(),
          )
        ) {
          const customHandler = Object.entries(statusResponseHandler).find(
            ([key, val]) => key.toString() === err.response.status.toString(),
          )[1]
          customHandler(error)
        } else {
          console.error(err.response)
          logError(err.response, ErrorType.AXIOS, errorObj.stack)
          toast(Languages.GeneralError)
        }
      } else if (err.request) {
        console.error(err)
        logError(err, ErrorType.AXIOS, errorObj.stack)
        toast(Languages.FatalError)
      } else {
        // We don't log error since this is probably network issue
        console.error(err)
        toast(Languages.FatalError)
      }
      return undefined
    } catch (e) {
      console.error(e)
      logError(e, ErrorType.UNHANDLED_AXIOS, errorObj.stack)
      throw e
    }
  }

// This is the default axios handler
const defaultErrorHandler = (error: CustomAxiosErrorType) => {
  return customErrorHandler()(error)
}

export default defaultErrorHandler
