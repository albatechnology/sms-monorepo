import Languages from "helper/languages"

import { logError, CustomAxiosErrorType } from "./"
import { ErrorType } from "./errorType"

export default (error: CustomAxiosErrorType) => {
  const errorObj = new Error()

  try {
    if (error instanceof Error) {
      logError(error, ErrorType.UNHANDLED_AXIOS, errorObj.stack)
      throw error
    }

    const { axiosError: err, loggedIn } = error

    if (err.response) {
      if (
        (err.response.status === 401 || err.response.status === 422) &&
        !loggedIn
      ) {
        toast(Languages.LoginFail)
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
    return { data: undefined }
  } catch (e) {
    logError(e, ErrorType.UNHANDLED_AXIOS, errorObj.stack)
    throw e
  }
}
