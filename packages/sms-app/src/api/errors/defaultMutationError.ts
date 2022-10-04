import Languages from "helper/languages"

import { CustomAxiosErrorType, logError } from "."
import { ErrorType } from "./errorType"

type CustomErrorHandler = Record<number, (error: CustomAxiosErrorType) => any>

const defaultMutationErrorHandler =
  (statusResponseHandler?: CustomErrorHandler, throwError: boolean = false) =>
  (error: CustomAxiosErrorType) => {
    const errorObj = new Error()
    try {
      if (error instanceof Error) {
        logError(error, ErrorType.UNHANDLED_AXIOS, errorObj.stack)
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
        } else if (err.response.status === 403 && err.response?.data?.message) {
          logError(err.response, ErrorType.AXIOS, errorObj.stack)
          toast(err.response?.data?.message + " (403 Error)")
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
            ([key, val]) => key === err.response.status.toString(),
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
      if (throwError) {
        throw error
      }
      return
    } catch (e) {
      if (!!e?.axiosError) {
        // Handled
        throw e
      } else {
        // Unhandled
        console.error(e)
        logError(e, ErrorType.UNHANDLED_AXIOS, errorObj.stack)
        throw e
      }
    }
  }

export default defaultMutationErrorHandler
