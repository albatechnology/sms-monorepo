import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { Button, Div } from "react-native-magnus"

import Text from "components/Text"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatCurrency } from "helper"

import { Order } from "types/Order"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "ActivityDetail">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

type PropTypes = {
  orderData: Order
}
export default function OrderPaymentDetail({ orderData }: PropTypes) {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  return (
    <Div bg="white">
      <Div px={20} mb={10} row justifyContent="space-between">
        <Text>Total Paid</Text>
        <Text>{formatCurrency(orderData.amountPaid)}</Text>
      </Div>

      <Div px={20} mb={10} row justifyContent="space-between">
        <Text>Total Unpaid</Text>
        <Text color="red">
          {formatCurrency(orderData.totalPrice - orderData.amountPaid)}
        </Text>
      </Div>

      <Button
        alignSelf="flex-end"
        justifyContent="flex-end"
        mx={20}
        mb={10}
        px={0}
        bg="white"
        color="blue500"
        underlayColor="blue100"
        fontSize="sm"
        onPress={() => {
          // @ts-ignore
          navigation.navigate("Customer", {
            screen: "OrderPaymentInfo",
            params: {
              orderId: orderData.id,
              companyId: orderData.companyId,
            },
          })
        }}
      >
        See Payment Details
      </Button>
    </Div>
  )
}
