import { add } from "date-fns"
import { Formik, FormikHelpers } from "formik"
import React from "react"
import CurrencyInput from "react-native-currency-input"
import { Button, Div, Input } from "react-native-magnus"
import * as Yup from "yup"

import BrandDropdownInput from "components/BrandDropdownInput"
import BrandEstimatedDropdown from "components/BrandEstimatedDropdown"
import DateTimePickerInput from "components/DateTimePickerInput"
import DropdownInput from "components/DropdownInput"
import ErrorMessage from "components/FormErrorMessage"
import Loading from "components/Loading"
import SelectInteriorActivity from "components/SelectInteriorActivity"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import {
  ActivityFollowUpMethodList,
  ActivityStatusList,
  ActivityFollowUpMethodReadOnlyList,
} from "api/generated/enums"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import s, { COLOR_DISABLED } from "helper/theme"

import { ActivityCoreData } from "types/Activity"

export type ActivityFormInput = ActivityCoreData & {
  brandIds: number[]
  interiorDesign: number
}

type PropTypes = {
  initialValues?: ActivityFormInput
  onSubmit?: (
    values: ActivityFormInput,
    formikHelpers: FormikHelpers<any>,
  ) => void | Promise<any>
  submitButtonText?: string
  isEditing?: boolean
}

const initialVal: ActivityFormInput = {
  followUpMethod: null,
  status: null,
  brandIds: [],
  estimatedValue: [],
  interiorDesign: "",
  feedback: "",
  reminderDateTime: null,
  reminderNote: "",
}
const validationSchema = Yup.object().shape({
  followUpMethod: Yup.string()
    .oneOf(ActivityFollowUpMethodList, "Mohon pilih follow up method")
    .typeError("Mohon pilih follow up method")
    .required("Mohon pilih follow up method"),
  status: Yup.string()
    .oneOf(ActivityStatusList, "Mohon pilih status")
    .typeError("Mohon pilih status")
    .required("Mohon pilih status"),
  interiorDesign: Yup.number().nullable(),
  brandIds: Yup.array()
    .min(1, "Mohon Pilih Brand List")
    .of(Yup.number())
    .required("Mohon Pilih Brand List")
    .typeError("Mohon Pilih Brand List"),
  estimatedValue: Yup.number().transform((value) =>
    isNaN(value) ? undefined : value,
  ),
  // .when("status", {
  //   is: "HOT",
  //   then: Yup.number()
  //     .transform((value) => (isNaN(value) ? undefined : value))
  //     .required("Mohon input estimation value"),
  // }),

  feedback: Yup.string()
    .min(20, "Wajib mengisi minimal 20 character")
    .required("Mohon isi feedback"),
  reminderDateTime: Yup.date()
    .typeError("Tanggal tidak valid")
    .nullable()
    .optional(),
  reminderNote: Yup.string().nullable().optional(),
})

export default ({
  initialValues = initialVal,
  onSubmit = () => Promise.resolve(),
  submitButtonText = "Add",
  isEditing = false,
}: PropTypes) => {
  const {
    queries: [{ data: userData }],
    meta: { isError, isLoading, isFetching },
  } = useMultipleQueries([useUserLoggedInData()] as const)
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
            Activity Follow up method
            <Text color="red">*</Text>
          </Text>
          <DropdownInput
            data={ActivityFollowUpMethodList.filter(
              (item) => !ActivityFollowUpMethodReadOnlyList.includes(item),
            )}
            title="Follow up method"
            message="Please select activity follow up method"
            value={values.followUpMethod}
            onSelect={handleChange("followUpMethod")}
            disabled={isEditing}
          />
          <ErrorMessage name="followUpMethod" />
          <Text mt={20} mb={10}>
            Status
            <Text color="red">*</Text>
          </Text>
          <DropdownInput
            data={ActivityStatusList}
            title="Status"
            message="Please select activity's status"
            value={values.status}
            onSelect={handleChange("status")}
            disabled={isEditing}
          />
          <ErrorMessage name="status" />
          <Text mt={20} mb={10}>
            Brand List<Text color="red">*</Text>
          </Text>
          <BrandEstimatedDropdown
            status={values.status}
            value={values.brandIds}
            onSelect={(val) => {
              setFieldValue("brandIds", val)
            }}
            profile={userData}
            disabled={!values.status ? true : false}
            setEstimation={(val) => setFieldValue("estimatedValue", val)}
            multiple
          />
          {/* <Text>{values.brandIds.length}</Text> */}
          <ErrorMessage name="brandIds" />
          <Text mt={20} mb={10}>
            Interior Design
          </Text>
          <SelectInteriorActivity
            value={values.interiorDesign}
            title="Interior Design List"
            message="Interior Design"
            onSelect={(val) => setFieldValue("interiorDesign", val)}
          />
          <ErrorMessage name="interiorDesign" />
          <Text mt={20} mb={10}>
            Feedback<Text color="red">*</Text>
          </Text>
          <Input
            placeholder="Input feedback here"
            placeholderTextColor="grey"
            value={values.feedback}
            onChangeText={handleChange("feedback")}
            onBlur={handleBlur("feedback")}
            multiline={true}
            borderColor="grey"
            textAlignVertical="top"
            numberOfLines={20}
            mb={5}
            scrollEnabled={false}
          />
          <ErrorMessage name="feedback" />

          <Text mt={20} mb={10}>
            Set Reminder Date (Optional)
          </Text>
          <DateTimePickerInput
            placeholder="Please select date"
            value={!!values.reminderDateTime ? values.reminderDateTime : ""}
            onSelect={(date) => {
              setFieldValue("reminderDateTime", date)
              if (!values.reminderDateTime) {
                setFieldValue("reminderNote", "")
              }
            }}
            minimumDate={new Date()}
            maximumDate={add(new Date(), { months: 3 })}
          />
          <ErrorMessage name="reminderDateTime" />

          {!!values.reminderDateTime && (
            <>
              <Text mt={20} mb={10}>
                Note
                <Text color="red">*</Text>
              </Text>
              <Input
                placeholder="Input note here"
                placeholderTextColor="grey"
                value={values.reminderNote}
                onChangeText={handleChange("reminderNote")}
                onBlur={handleBlur("reminderNote")}
                multiline={true}
                borderColor="grey"
                scrollEnabled={false}
                textAlignVertical="top"
                numberOfLines={5}
                mb={5}
              />
              <ErrorMessage name="reminderNote" />
            </>
          )}

          {isSubmitting ? (
            <Loading />
          ) : (
            <Button
              loading={isSubmitting}
              onPress={() => handleSubmit()}
              bg="primary"
              mt={30}
              px={20}
              disabled={
                userData.type === "DIRECTOR" &&
                userData.app_create_lead === false
                  ? true
                  : false
              }
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
