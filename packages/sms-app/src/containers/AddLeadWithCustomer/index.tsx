import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
  RouteProp,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"

import LeadWithNewCustomerForm from "forms/LeadWithNewCustomerForm"

import useCustomerCreateWithAddressMutation from "api/hooks/customer/useCustomerCreateWithAddressMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { responsive } from "helper"

type CurrentScreenRouteProp = RouteProp<
  CustomerStackParamList,
  "AddLeadWithCustomer"
>
type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "AddLeadWithCustomer">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [createCustomerWithAddress] = useCustomerCreateWithAddressMutation()

  const type = route?.params?.type ?? null
  const isUnhandled = route?.params?.isUnhandled ?? false

  return (
    <CustomKeyboardAvoidingView
      style={{ flex: 1 }}
      additionalOffset={-responsive(75)}
    >
      <LeadWithNewCustomerForm
        onSubmit={async (data) => {
          await createCustomerWithAddress(
            {
              firstName: data.firstName,
              lastName: data.lastName,
              dateOfBirth: data.dateOfBirth,
              email: data.email,
              phone: data.phone,
              description: data.description,
              title: data.title,
              addressLine1: data.addressLine1,
              addressLine2: data.addressLine2,
              addressLine3: data.addressLine3,
              city: data.city,
              province: data.province,
              postcode: data.postcode,
              country: data.country,
              type: data.type,
            },
            (x) =>
              x.then((res) => {
                navigation.reset({
                  index: 1,
                  routes: [
                    {
                      name: "CustomerList",
                    },
                    {
                      name: "AddLead",
                      params: {
                        customerId: res.data.data.id,
                        type,
                        isUnhandled,
                      },
                    },
                  ],
                })
              }),
          )
        }}
      />
    </CustomKeyboardAvoidingView>
  )
}
