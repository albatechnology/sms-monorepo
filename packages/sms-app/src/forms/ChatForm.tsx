import { Formik, FormikHelpers } from "formik"
import React from "react"
import { Button, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import ErrorMessage from "components/FormErrorMessage"
import Loading from "components/Loading"
import Text from "components/Text"
import UserDropdownInput from "components/UserDropdownInput"

type ChatInput = {
  subject: string
  users: number[]
}

type PropTypes = {
  initialValues?: ChatInput
  onSubmit?: (
    values: ChatInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
}

const initialVal: ChatInput = {
  subject: "",
  users: [],
}

const validationSchema = Yup.object().shape({
  subject: Yup.string().required("Mohon isi topic"),
  users: Yup.array(Yup.number()).min(1).required("Mohon pilih user"),
})

export default ({
  initialValues = initialVal,
  onSubmit = () => Promise.resolve(),
  submitButtonText = "Add",
}: PropTypes) => {
  return (
    <Formik
      validationSchema={validationSchema}
      initialValues={initialValues}
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
        <Div w={"100%"} p={20}>
          <Text mb={10}>
            Subject <Text color="red">*</Text>
          </Text>
          <Input
            placeholder="Input subject here"
            placeholderTextColor="grey"
            value={values.feedback}
            onChangeText={handleChange("subject")}
            onBlur={handleBlur("subject")}
            borderColor="grey"
            mb={5}
          />
          <ErrorMessage name="subject" />

          <Text mt={20} mb={10}>
            Users
            <Text color="red">*</Text>
          </Text>
          <UserDropdownInput
            multiple={true}
            value={values.users}
            onSelect={(val) => setFieldValue("users", val)}
          />
          <ErrorMessage name="users" />

          {isSubmitting ? (
            <Loading />
          ) : (
            <Button
              loading={isSubmitting}
              disabled={isSubmitting}
              onPress={() => handleSubmit()}
              bg="primary"
              mt={30}
              px={20}
              alignSelf="center"
              w={"100%"}
            >
              <Text fontWeight="bold" color="white">
                {submitButtonText}
              </Text>
            </Button>
          )}
        </Div>
      )}
    </Formik>
  )
}
