import { Formik, FormikHelpers } from "formik"
import React from "react"
import { Button, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import CityProvinceDropdown from "components/CityProvinceDropdownInput"
import CountryDropdown from "components/CountryDropdownInput"
import DropdownInput from "components/DropdownInput"
import ErrorMessage from "components/FormErrorMessage"
import Loading from "components/Loading"
import Text from "components/Text"

import { AddressTypeList } from "api/generated/enums"

import { cityNameList } from "helper/data/cities"
import { provinceNameList } from "helper/data/provinces"

import { Address } from "types/Address"

export type AddressFormInput = Omit<Address, "id" | "customerId">

type PropTypes = {
  initialValues?: AddressFormInput
  onSubmit?: (
    values: AddressFormInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
}

const initialVal: AddressFormInput = {
  addressLine1: "",
  addressLine2: "",
  addressLine3: "",
  country: "Indonesia",
  city: "",
  province: "",
  postcode: "",
  phone: "",
  type: "ADDRESS",
}

const validationSchema = Yup.object().shape({
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
  phone: Yup.string().min(5).nullable().optional(),
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
        <Div w={"100%"} p={20}>
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

          {isSubmitting ? (
            <Loading />
          ) : (
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
          )}
        </Div>
      )}
    </Formik>
  )
}
