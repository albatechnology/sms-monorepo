import { Formik, FormikHelpers } from "formik"
import React from "react"
import { Button, Checkbox, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import CustomerDropdownInput from "components/CustomerDropdownInput"
import DropdownInput from "components/DropdownInput"
import ErrorMessage from "components/FormErrorMessage"
import LeadCategoryDropdownInput from "components/LeadCategoryDropdownInput"
import Loading from "components/Loading"
import Text from "components/Text"

import { LeadTypeList } from "api/generated/enums"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { Customer } from "types/Customer"
import { Lead } from "types/Lead"
import { LeadCategory } from "types/LeadCategory"

export type LeadFormInput = Pick<
  Lead,
  "type" | "label" | "isUnhandled" | "interest"
> & {
  customerId: Customer["id"]
  leadCategoryId: LeadCategory["id"]
}

type PropTypes = {
  initialValues?: Partial<LeadFormInput>
  onSubmit?: (
    values: LeadFormInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
  isEditing?: boolean
  interest?: string
}

const initialVal: LeadFormInput = {
  type: "LEADS",
  label: "",
  interest: "",
  customerId: null,
  leadCategoryId: null,
  isUnhandled: false,
}

const validationSchema = Yup.object().shape({
  type: Yup.string()
    .oneOf(LeadTypeList.concat(null), "Lead Type invalid")
    .typeError("Mohon pilih type")
    .required("Mohon pilih type"),
  label: Yup.string().min(2).max(100).nullable().optional(),
  customerId: Yup.number()
    .typeError("Mohon pilih customer")
    .required("Mohon pilih customer"),
  leadCategoryId: Yup.number()
    .typeError("Mohon pilih lead category")
    .required("Mohon pilih lead category"),
  isUnhandled: Yup.bool(),
  interest: Yup.string().min(2).max(200).nullable().optional(),
})

export default ({
  initialValues = initialVal,
  onSubmit = () => Promise.resolve(),
  submitButtonText = "Add",
  isEditing = false,
}: PropTypes) => {
  const { data } = useUserLoggedInData()

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
        errors,
      }) => (
        <Div w={"100%"} p={20}>
          <Text mb={10}>
            Customer<Text color="red">*</Text>
          </Text>
          <CustomerDropdownInput
            value={values.customerId}
            onSelect={handleChange("customerId")}
            disabled={isEditing}
            searchOnly={true}
          />
          <ErrorMessage name="customerId" />

          <Text mt={20} mb={10}>
            Type<Text color="red">*</Text>
          </Text>
          <DropdownInput
            data={LeadTypeList}
            title="Type"
            message="Please select the lead type"
            value={values.type}
            onSelect={handleChange("type")}
            disabled={isEditing}
          />
          <ErrorMessage name="type" />

          <Text mt={20} mb={10}>
            Label (Optional)
          </Text>
          <Input
            placeholder="Input your label here"
            placeholderTextColor="grey"
            value={values.label}
            onChangeText={handleChange("label")}
            onBlur={handleBlur("label")}
            borderColor="grey"
            mb={5}
          />
          <ErrorMessage name="label" />
          <Text mt={20} mb={10}>
            Interest (Optional)
          </Text>
          <Input
            placeholder="Input your lead interest"
            placeholderTextColor="grey"
            onChangeText={handleChange("interest")}
            onBlur={handleBlur("interest")}
            borderColor="grey"
            mb={5}
            value={values.interest}
          />
          <ErrorMessage name="interest" />

          <Text mt={20} mb={10}>
            Lead Category<Text color="red">*</Text>
          </Text>
          <LeadCategoryDropdownInput
            value={values.leadCategoryId}
            onSelect={handleChange("leadCategoryId")}
            disabled={isEditing}
          />
          <ErrorMessage name="leadCategoryId" />
          {data.type !== "SALES" && (
            <>
              <Text mt={20} mb={10}>
                Is Unhandled
              </Text>
              <Checkbox
                checked={values.isUnhandled}
                onChecked={(checked) => {
                  setFieldValue("isUnhandled", checked)
                }}
                mb={5}
              />
              <ErrorMessage name="isUnhandled" />
            </>
          )}

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
