import { Formik, FormikHelpers } from "formik"
import React from "react"
import { Button, Icon, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import ErrorMessage from "components/FormErrorMessage"

import { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

export type FormInput = { chat: string }

type PropTypes = {
  onSubmit?: (
    values: FormInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
}

const validationSchema = Yup.object().shape({
  chat: Yup.string().required("Cannot be empty"),
})

export default ({ onSubmit = () => Promise.resolve() }: PropTypes) => {
  return (
    <Formik
      validationSchema={validationSchema}
      initialValues={{}}
      validateOnBlur
      onSubmit={onSubmit}
      enableReinitialize
    >
      {({
        handleChange,
        handleBlur,
        handleSubmit,
        values,
        isSubmitting,
        setFieldValue,
      }) => (
        <Div w="100%" px={20} py={10} shadow="lg" bg="white">
          <Div row>
            <Input
              flex={1}
              placeholder="Type something here..."
              placeholderTextColor={COLOR_PRIMARY}
              value={values.chat}
              onChangeText={handleChange("chat")}
              onBlur={handleBlur("chat")}
              borderColor="grey"
              bg={COLOR_DISABLED}
              multiline={true}
              scrollEnabled={false}
            />

            <Div justifyContent="center">
              <Button
                borderless
                bg="primary"
                p={10}
                rounded="circle"
                ml={10}
                onPress={() => handleSubmit()}
                loading={isSubmitting}
              >
                <Icon
                  name="send"
                  color="white"
                  fontSize={16}
                  fontFamily="Ionicons"
                />
              </Button>
            </Div>
          </Div>
          <ErrorMessage name="chat" />
        </Div>
      )}
    </Formik>
  )
}
