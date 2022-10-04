import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { FlatList } from "react-native"
import { Button, Div, Input } from "react-native-magnus"

import AddressView from "components/AddressView"
import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { customErrorHandler } from "api/errors"
import useOrderById from "api/hooks/order/useOrderById"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatCurrency } from "helper"
import Languages from "helper/languages"

import CheckoutItem from "./CheckoutItem"

type CurrentScreenRouteProp = RouteProp<CustomerStackParamList, "Payment">
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "Payment">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const orderId = route?.params?.orderId ?? -1
  if (orderId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Main")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: orderData }],
    meta: { isError, isLoading, isFetching, refetch },
  } = useMultipleQueries([
    useOrderById(
      orderId,
      {},
      customErrorHandler({
        404: () => {
          toast("Order tidak ditemukan")
          if (navigation.canGoBack()) {
            navigation.goBack()
          } else {
            navigation.navigate("Main")
          }
        },
      }),
    ),
  ] as const)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  const Header = (
    <>
      <Div>
        <AddressBlock
          title="Shipping Address"
          address={orderData?.shippingAddress}
        />
        <AddressBlock
          mt={5}
          title="Billing Address"
          address={orderData?.billingAddress}
        />
      </Div>
      <Div
        mt={5}
        p={20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor="grey"
      >
        <Text fontSize={14} fontWeight="bold">
          Product
        </Text>
      </Div>
      <Div bg="white" pb={20} />
    </>
  )

  const Footer = (
    <>
      <Div
        mt={5}
        p={20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor="grey"
      >
        <Text fontSize={14} fontWeight="bold">
          Order Detail
        </Text>
      </Div>
      <>
        <Div p={20} bg="white">
          <Div row justifyContent="space-between" mb={5}>
            <Text>Price</Text>
            <Text>{formatCurrency(orderData?.originalPrice)}</Text>
          </Div>
          {!!orderData?.discountId && (
            <Div row justifyContent="space-between" mb={5}>
              <Text>Discount</Text>
              <Text>{formatCurrency(-1 * orderData?.totalDiscount)}</Text>
            </Div>
          )}
          <Div row justifyContent="space-between" mb={5}>
            <Text>Packing</Text>
            <Text>{formatCurrency(orderData?.packingFee)}</Text>
          </Div>
          <Div row justifyContent="space-between">
            <Text>Shipping</Text>
            <Text>{formatCurrency(orderData?.shippingFee)}</Text>
          </Div>
        </Div>
        <Div p={20} bg="white" borderTopWidth={0.8} borderTopColor="grey">
          <Div row justifyContent="space-between">
            <Text fontWeight="bold">Sub Total</Text>
            <Text fontWeight="bold">
              {formatCurrency(orderData?.totalPrice)}
            </Text>
          </Div>
        </Div>
      </>

      <Div
        mt={5}
        p={20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor="grey"
      >
        <Text fontSize={14} fontWeight="bold">
          Note (optional)
        </Text>
      </Div>
      <Div px={20} py={10} bg="white">
        <Input
          placeholder={`${orderData?.note || "--Empty--"}`}
          placeholderTextColor="grey"
          value={orderData?.note}
          onChangeText={() => {}}
          multiline={true}
          borderColor="grey"
          textAlignVertical="top"
          numberOfLines={5}
          scrollEnabled={false}
          editable={false}
        />
      </Div>

      <Div mt={5} p={20} bg="white">
        <Button
          onPress={() => {
            navigation.navigate("PaymentPayCategorySelection", { orderId })
          }}
          bg="primary"
          alignSelf="center"
          w={"100%"}
          disabled={isLoading}
        >
          <Text fontWeight="bold" color="white">
            Proceed to payment
          </Text>
        </Button>
      </Div>
    </>
  )

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <FlatList
        contentContainerStyle={{ flexGrow: 1 }}
        data={orderData?.orderDetails || []}
        keyExtractor={(item, index) => `cart_item_${index}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListHeaderComponent={Header}
        ListFooterComponent={Footer}
        renderItem={({ item, index }) => <CheckoutItem item={item} />}
      />
    </CustomKeyboardAvoidingView>
  )
}

const AddressBlock = ({ title, address, ...rest }) => {
  return (
    <Div {...rest}>
      <Div bg="white" p={20} borderBottomWidth={0.8} borderBottomColor="grey">
        <Text fontSize={14} fontWeight="bold">
          {title}
        </Text>
      </Div>
      <Div p={20} bg="white">
        <AddressView address={address} />
      </Div>
    </Div>
  )
}
