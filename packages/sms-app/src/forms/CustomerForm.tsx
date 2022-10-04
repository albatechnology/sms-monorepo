import { sub } from "date-fns"
import { Formik, FormikHelpers } from "formik"
import React from "react"
import { Button, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import AddressSelectorInput from "components/AddressSelectorInput"
import DatePickerInput from "components/DatePickerInput"
import DropdownInput from "components/DropdownInput"
import ErrorMessage from "components/FormErrorMessage"
import Text from "components/Text"

import { PersonTitle, PersonTitleList } from "api/generated/enums"

import { Customer } from "types/Customer"

type CustomerInput = Omit<Customer, "id">

type PropTypes = {
  initialValues?: CustomerInput
  onSubmit?: (
    values: CustomerInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
  customerId?: number
}

const initialVal: CustomerInput = {
  title: "" as PersonTitle,
  firstName: "",
  lastName: "",
  dateOfBirth: null,
  description: "",
  phone: "",
  email: "",
  defaultAddressId: -1,
}

const validationSchema = Yup.object().shape({
  title: Yup.string()
    .oneOf(PersonTitleList, "Mohon pilih title")
    .typeError("Mohon pilih title")
    .required("Mohon pilih title"),
  firstName: Yup.string().min(2).max(100).required("Mohon isi nama depan"),
  lastName: Yup.string().min(2).max(100).nullable().optional(),
  dateOfBirth: Yup.date()
    .typeError("Tanggal tidak valid")
    .nullable()
    .optional(),
  description: Yup.string().max(225).nullable().optional(),
  phone: Yup.string().max(25).nullable().optional(),
  email: Yup.string().email("Email tidak valid").nullable().optional(),
  defaultAddressId: Yup.number().nullable().optional(),
})

export default ({
  initialValues = initialVal,
  onSubmit = () => Promise.resolve(),
  submitButtonText = "Add",
  customerId,
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
        <Div w={"100%"}>
          <Div
            bg="white"
            p={20}
            borderBottomWidth={0.8}
            borderBottomColor="grey"
          >
            <Text fontSize={14} fontWeight="bold">
              Basic Information
            </Text>
          </Div>
          <Div bg="white" p={20} mb={5}>
            <Text mb={10}>
              Title
              <Text color="red">*</Text>
            </Text>
            <DropdownInput
              data={PersonTitleList}
              title="Title"
              message="Please select your title"
              value={values.title}
              onSelect={handleChange("title")}
            />

            <ErrorMessage name="title" />

            <Text mt={20} mb={10}>
              First Name
              <Text color="red">*</Text>
            </Text>
            <Input
              placeholder="Input first name here"
              placeholderTextColor="grey"
              value={values.firstName}
              onChangeText={handleChange("firstName")}
              onBlur={handleBlur("firstName")}
              borderColor="grey"
              mb={5}
            />
            <ErrorMessage name="firstName" />

            <Text mt={20} mb={10}>
              Last Name
            </Text>
            <Input
              placeholder="Input last name here"
              placeholderTextColor="grey"
              value={values.lastName}
              onChangeText={handleChange("lastName")}
              onBlur={handleBlur("lastName")}
              borderColor="grey"
              mb={5}
            />
            <ErrorMessage name="lastName" />

            <Text mt={20} mb={10}>
              Date of Birth
            </Text>
            <DatePickerInput
              placeholder="Please select date of birth"
              value={values.dateOfBirth}
              onSelect={(date) => setFieldValue("dateOfBirth", date)}
              minimumDate={null}
              maximumDate={sub(new Date(), { years: 18 })}
            />
            <ErrorMessage name="dateOfBirth" />

            <Text mt={20} mb={10}>
              Description
            </Text>
            <Input
              placeholder="Input description here"
              placeholderTextColor="grey"
              value={values.description}
              onChangeText={handleChange("description")}
              onBlur={handleBlur("description")}
              borderColor="grey"
              mb={5}
            />
            <ErrorMessage name="description" />

            <Text mt={20} mb={10}>
              Phone Number
            </Text>
            <Input
              placeholder="Input phone number here"
              placeholderTextColor="grey"
              value={values.phone}
              onChangeText={handleChange("phone")}
              onBlur={handleBlur("phone")}
              keyboardType="phone-pad"
              borderColor="grey"
              mb={5}
            />
            <ErrorMessage name="phone" />

            <Text mt={20} mb={10}>
              Email
            </Text>
            <Input
              placeholder="Input email here"
              placeholderTextColor="grey"
              value={values.email}
              onChangeText={handleChange("email")}
              onBlur={handleBlur("email")}
              borderColor="grey"
              mb={5}
            />
            <ErrorMessage name="email" />
          </Div>

          {customerId && (
            <>
              <AddressSelectorInput
                customerId={customerId}
                value={values.defaultAddressId}
                onSelect={handleChange("defaultAddressId")}
              />
              <ErrorMessage name="defaultAddressId" />
            </>
          )}

          <Div bg="white" mt={5}>
            <Button
              block
              loading={isSubmitting}
              disabled={isSubmitting}
              onPress={() => handleSubmit()}
              bg="primary"
              m={20}
              alignSelf="center"
            >
              <Text fontWeight="bold" color="white">
                {submitButtonText}
              </Text>
            </Button>
          </Div>
        </Div>
      )}
    </Formik>
  )
}
