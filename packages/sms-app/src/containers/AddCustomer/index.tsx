import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { ScrollView } from "react-native"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"

import CustomerForm from "forms/CustomerForm"

import useCustomerCreateMutation from "api/hooks/customer/useCustomerCreateMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "AddCustomer">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [createCustomer] = useCustomerCreateMutation()

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <ScrollView
        contentContainerStyle={[
          {
            flexGrow: 1,
            alignItems: "center",
          },
        ]}
      >
        <CustomerForm
          onSubmit={async (data) => {
            await createCustomer(
              {
                firstName: data.firstName,
                lastName: data.lastName,
                email: data.email,
                phone: data.phone,
                description: data.description,
                title: data.title,
                dateOfBirth: data.dateOfBirth,
              },
              (x) =>
                x.then(() => {
                  navigation.reset({
                    index: 0,
                    routes: [{ name: "CustomerList" }],
                  })
                }),
            )
          }}
        />
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
