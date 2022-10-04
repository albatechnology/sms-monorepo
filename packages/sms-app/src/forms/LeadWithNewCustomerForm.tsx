import { sub } from "date-fns"
import { Formik, FormikHelpers } from "formik"
import React from "react"
import { ScrollView } from "react-native"
import { Button, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import CityProvinceDropdown from "components/CityProvinceDropdownInput"
import CountryDropdown from "components/CountryDropdownInput"
import DatePickerInput from "components/DatePickerInput"
import DropdownInput from "components/DropdownInput"
import ErrorMessage from "components/FormErrorMessage"
import Text from "components/Text"

import {
  AddressTypeList,
  PersonTitle,
  PersonTitleList,
} from "api/generated/enums"

import { cityNameList } from "helper/data/cities"
import { provinceNameList } from "helper/data/provinces"

import { Address } from "types/Address"
import { Customer } from "types/Customer"

export type LeadWithNewCustomerFormInput = Omit<
  Customer,
  "id" | "defaultAddressId"
> &
  Omit<Address, "id" | "customerId">

type PropTypes = {
  initialValues?: LeadWithNewCustomerFormInput
  onSubmit?: (
    values: LeadWithNewCustomerFormInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
}

const initialVal: LeadWithNewCustomerFormInput = {
  title: "" as PersonTitle,
  firstName: "",
  lastName: "",
  dateOfBirth: null,
  description: "",
  phone: "",
  email: "",
  addressLine1: "",
  addressLine2: "",
  addressLine3: "",
  country: "Indonesia",
  city: "",
  province: "",
  postcode: "",
  type: "ADDRESS",
}

const validationSchema = Yup.object().shape({
  title: Yup.string()
    .oneOf(PersonTitleList, "Mohon pilih title")
    .required("Mohon isi title"),
  firstName: Yup.string().min(2).max(100).required("Mohon isi nama depan"),
  lastName: Yup.string().min(2).max(100).optional().nullable(),
  dateOfBirth: Yup.date()
    .typeError("Tanggal tidak valid")
    .nullable()
    .optional(),
  description: Yup.string().max(225).optional().nullable(),
  phone: Yup.string()
    // .matches(/^08/, { message: "Nomor tidak valid" })
    .required("Mohon isi nomor HP"),
  email: Yup.string().email("Email tidak valid").required("Mohon isi email"),
  addressLine1: Yup.string().min(5).required("Mohon isi alamat"),
  addressLine2: Yup.string().min(5).nullable().optional(),
  addressLine3: Yup.string().min(5).nullable().optional(),
  postcode: Yup.string().min(2).max(10).nullable().optional(),
  country: Yup.string().min(2).nullable().optional(),
  city: Yup.string()
    .min(2)
    .oneOf(
      cityNameList,
      "Kota tidak valid, mohon pilih dari opsi yang tersedia",
    )
    .nullable()
    .optional(),
  province: Yup.string()
    .min(2)
    .oneOf(
      provinceNameList,
      "Provinsi tidak valid, mohon pilih dari opsi yang tersedia",
    )
    .nullable()
    .optional(),
  type: Yup.string()
    .oneOf(AddressTypeList, "Mohon pilih tipe alamat")
    .typeError("Mohon pilih tipe alamat")
    .required("Mohon pilih tipe alamat"),
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
        <>
          <ScrollView
            contentContainerStyle={[
              {
                flexGrow: 1,
                alignItems: "center",
              },
            ]}
          >
            <Div
              w="100%"
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
                <Text color="red">*</Text>
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
                <Text color="red">*</Text>
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

            <Div
              w="100%"
              bg="white"
              p={20}
              borderBottomWidth={0.8}
              borderBottomColor="grey"
            >
              <Text fontSize={14} fontWeight="bold">
                Address Information
              </Text>
            </Div>
            <Div bg="white" px={20} pt={20} pb="25%">
              <Text mb={10}>
                Address Line 1<Text color="red">*</Text>
              </Text>
              <Input
                placeholder="Input your address here"
                placeholderTextColor="grey"
                value={values.addressLine1}
                onChangeText={handleChange("addressLine1")}
                onBlur={handleBlur("addressLine1")}
                borderColor="grey"
                mb={5}
              />
              <ErrorMessage name="addressLine1" />

              <Text mt={20} mb={10}>
                Address Line 2
              </Text>
              <Input
                placeholder="Input your address here"
                placeholderTextColor="grey"
                value={values.addressLine2}
                onChangeText={handleChange("addressLine2")}
                onBlur={handleBlur("addressLine2")}
                borderColor="grey"
                mb={5}
              />
              <ErrorMessage name="addressLine2" />

              <Text mt={20} mb={10}>
                Address Line 3
              </Text>
              <Input
                placeholder="Input your address here"
                placeholderTextColor="grey"
                value={values.addressLine3}
                onChangeText={handleChange("addressLine3")}
                onBlur={handleBlur("addressLine3")}
                borderColor="grey"
                mb={5}
              />
              <ErrorMessage name="addressLine3" />

              <Text mt={20} mb={10}>
                Province
              </Text>
              <CityProvinceDropdown
                title="Province"
                message="Please select your province"
                value={values.province}
                onSelect={(...val) => {
                  handleChange("province")(...val)
                  setFieldValue("city", "")
                }}
              />
              <ErrorMessage name="province" />

              <Text mt={20} mb={10}>
                City
              </Text>
              <CityProvinceDropdown
                provinceName={values.province}
                title="City"
                message="Please select your city"
                value={values.city}
                onSelect={handleChange("city")}
                disabled={!provinceNameList.includes(values.province)}
              />
              <ErrorMessage name="city" />

              <Text mt={20} mb={10}>
                Country
              </Text>
              <CountryDropdown
                value={values.country}
                onSelect={handleChange("country")}
              />
              <ErrorMessage name="country" />

              <Text mt={20} mb={10}>
                Post Code
              </Text>
              <Input
                placeholder="Input post code here"
                placeholderTextColor="grey"
                value={values.postcode}
                onChangeText={handleChange("postcode")}
                onBlur={handleBlur("postcode")}
                keyboardType="number-pad"
                borderColor="grey"
                mb={5}
              />
              <ErrorMessage name="postcode" />

              <Text mt={20} mb={10}>
                Address Type<Text color="red">*</Text>
              </Text>
              <DropdownInput
                data={AddressTypeList}
                title="Address Type"
                message="Please select the address type"
                value={values.type}
                onSelect={handleChange("type")}
              />
              <ErrorMessage name="type" />
            </Div>
          </ScrollView>

          <Div bg="white" position="absolute" bottom={0} shadow="md">
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
        </>
      )}
    </Formik>
  )
}
