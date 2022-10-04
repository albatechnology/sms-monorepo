import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { RefreshControl, ScrollView } from "react-native"
import { Div } from "react-native-magnus"

import CheckoutItem from "containers/Payment/CheckoutItem"

import Error from "components/Error"
import Loading from "components/Loading"
import OrderPaymentDetail from "components/Order/OrderPaymentDetail"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import PaymentForm from "forms/PaymentForm"

import { customErrorHandler } from "api/errors"
import useOrderById from "api/hooks/order/useOrderById"
import usePaymentCreateMutation from "api/hooks/payment/usePaymentCreateMutation"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatCurrency } from "helper"
import Languages from "helper/languages"

import { orderPaymentStatusConfig } from "types/Order"

type CurrentScreenRouteProp = RouteProp<
  CustomerStackParamList,
  "PaymentPayConfirm"
>
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "PaymentPayConfirm">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const orderId = route?.params?.orderId ?? -1
  const paymentTypeId = route?.params?.paymentTypeId ?? -1
  if (orderId === -1 || paymentTypeId === -1) {
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
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      isManualRefetching,
      manualRefetch,
    },
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

  const [createPayment] = usePaymentCreateMutation()

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <ScrollView
      contentContainerStyle={[{ flexGrow: 1 }]}
      refreshControl={
        <RefreshControl
          refreshing={isManualRefetching}
          onRefresh={manualRefetch}
        />
      }
    >
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
      {(orderData?.orderDetails || []).map((item) => (
        <CheckoutItem item={item} />
      ))}

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
          <Text fontWeight="bold">{formatCurrency(orderData?.totalPrice)}</Text>
        </Div>
      </Div>

      <Div
        mt={5}
        p={20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor="grey"
      >
        <Text fontSize={14} fontWeight="bold">
          Payment Info
        </Text>
      </Div>
      <Div
        px={20}
        pt={20}
        pb={10}
        bg="white"
        row
        justifyContent="space-between"
      >
        <Text>Payment Status:</Text>
        <Text
          color={orderPaymentStatusConfig[orderData.paymentStatus].textColor}
        >
          {orderPaymentStatusConfig[orderData.paymentStatus].displayText}
        </Text>
      </Div>
      <OrderPaymentDetail orderData={orderData} />

      <PaymentForm
        onSubmit={(values) => {
          return createPayment(
            {
              amount: values.amount,
              reference: values.reference,
              orderId,
              paymentTypeId,
            },
            (x) =>
              x.then((res) => {
                const paymentId = res.data.data.id
                navigation.pop(3)
                navigation.replace("OrderPaymentProof", { paymentId })

                return res
              }),
          )
        }}
      />
    </ScrollView>
  )
}
