import { Formik, FormikHelpers } from "formik"
import React from "react"
import CurrencyInput from "react-native-currency-input"
import { Button, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import ErrorMessage from "components/FormErrorMessage"
import Text from "components/Text"

import s, { COLOR_DISABLED } from "helper/theme"

import { Payment } from "types/Payment/Payment"

export type LeadFormInput = Pick<Payment, "amount" | "reference">

type PropTypes = {
  initialValues?: Partial<LeadFormInput>
  onSubmit?: (
    values: LeadFormInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
  isEditing?: boolean
}

const initialVal: LeadFormInput = {
  amount: 0,
  reference: "",
}

const validationSchema = Yup.object().shape({
  amount: Yup.number()
    .min(1, "Total tidak valid")
    .typeError("Total hanya boleh berisi angka")
    .required("Mohon isi total"),
  reference: Yup.string().nullable().optional(),
})

const borderStyle = {
  borderWidth: 1,
  borderColor: COLOR_DISABLED,
}

export default ({
  initialValues = initialVal,
  onSubmit = () => Promise.resolve(),
  submitButtonText = "Confirm Payment",
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
        <Div w={"100%"} px={20} pb={20} bg="white">
          <Text mb={10}>
            Amount<Text color="red">*</Text>
          </Text>
          <CurrencyInput
            value={values.amount}
            onChangeValue={(val) =>
              !!val ? setFieldValue("amount", val) : setFieldValue("amount", 0)
            }
            prefix="Rp."
            delimiter="."
            separator=","
            precision={0}
            style={[s.bgWhite, s.p10, s.mB10, borderStyle]}
          />
          <ErrorMessage name="amount" />

          <Text mt={20} mb={10}>
            Reference (Optional)
          </Text>
          <Input
            placeholder="Input your reference here"
            placeholderTextColor="grey"
            value={values.reference}
            onChangeText={handleChange("reference")}
            onBlur={handleBlur("reference")}
            borderColor="grey"
            mb={5}
          />
          <ErrorMessage name="reference" />

          <Button
            block
            loading={isSubmitting}
            disabled={isSubmitting}
            onPress={() => handleSubmit()}
            bg="primary"
            my={20}
            alignSelf="center"
          >
            <Text fontWeight="bold" color="white">
              {submitButtonText}
            </Text>
          </Button>
        </Div>
      )}
    </Formik>
  )
}
