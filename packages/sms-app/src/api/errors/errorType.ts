export enum ErrorType {
  NATIVE = "Native Error",
  AXIOS = "Axios Error",
  UNHANDLED_AXIOS = "Unhandled Axios Error",
  CUSTOM = "Custom Error",
}

export const getErrorTypeDescription = (errorType: ErrorType) => {
  switch (errorType) {
    case ErrorType.NATIVE:
      return "Errors that comes from other parts of the code that is not from axios. This is likely an app issue instead of backend issue."
    case ErrorType.AXIOS:
      return "This error comes from axios. For example: response failed, invalid request, etc. This is likely backend issue."
    case ErrorType.UNHANDLED_AXIOS:
      return "This error is from handling Axios (server request). But somehow our Axios error handler didn't catch it. Could be error parsing data for example. This is likely backend issue."
    case ErrorType.CUSTOM:
      return "This error is generated manually. Mostly used for debugging purposes."
    default:
      return "Unknown error type. We should investigate this"
  }
}
