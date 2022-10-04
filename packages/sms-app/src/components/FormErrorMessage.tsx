import { ErrorMessage } from "formik"
import React from "react"
import { Div } from "react-native-magnus"

import Text from "components/Text"

import { COLOR_RED_TEXT } from "helper/theme"

export default ({ name }) => {
  return (
    <ErrorMessage name={name}>
      {(msg) => (
        <Div>
          <Text color={COLOR_RED_TEXT}>* {msg}</Text>
        </Div>
      )}
    </ErrorMessage>
  )
}
