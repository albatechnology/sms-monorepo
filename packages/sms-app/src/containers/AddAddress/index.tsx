import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  RouteProp,
  useRoute,
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { ScrollView } from "react-native"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"

import AddressForm from "forms/AddressForm"

import useAddressCreateMutation from "api/hooks/address/useAddressCreateMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "AddAddress">

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "AddAddress">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const customerId = route?.params?.customerId ?? -1
  if (customerId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const [createAddress] = useAddressCreateMutation()

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1, backgroundColor: "white" }}>
      <ScrollView
        contentContainerStyle={[
          {
            flexGrow: 1,
            alignItems: "center",
          },
        ]}
      >
        <AddressForm
          onSubmit={async (data) => {
            await createAddress(
              {
                addressLine1: data.addressLine1,
                addressLine2: data.addressLine2,
                addressLine3: data.addressLine3,
                postcode: data.postcode,
                city: data.city,
                province: data.province,
                country: data.country,
                phone: data.phone,
                type: data.type,
                customerId: customerId,
              },
              (x) =>
                x.then(() => {
                  navigation.goBack()
                }),
            )
          }}
        />
      </ScrollView>
    </CustomKeyboardAvoidingView>
  )
}
