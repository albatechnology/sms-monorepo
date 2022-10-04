import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  RouteProp,
  CompositeNavigationProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import * as FileSystem from "expo-file-system"
import * as Sharing from "expo-sharing"
import React, { useState } from "react"
import { FlatList, RefreshControl } from "react-native"
import { Button, Div, Text } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Loading from "components/Loading"
import Tag from "components/Tag"

import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import useCompanyById from "api/hooks/company/useCompanyById"
import useOrderById from "api/hooks/order/useOrderById"
import usePaymentList from "api/hooks/payment/usePaymentList"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatCurrency } from "helper"
import Languages from "helper/languages"
import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

import { Payment, paymentStatusConfig } from "types/Payment/Payment"

import GenerateReceiptPdf from "./GenerateReceipt"

type CurrentScreenRouteProp = RouteProp<
  CustomerStackParamList,
  "OrderPaymentInfo"
>
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "OrderPaymentInfo">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const orderId = route?.params?.orderId ?? -1
  const companyId = route?.params?.companyId ?? -1
  if (orderId === -1 || companyId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Main")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: orderData }, { data: paymentPaginatedData }],
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      isManualRefetching,
      manualRefetch,
      hasNextPage,
      fetchNextPage,
      isFetchingNextPage,
    },
  } = useMultipleQueries([
    useOrderById(orderId),
    usePaymentList({ filterOrderId: orderId.toString(), sort: "-id" }),
    useCompanyById(companyId),
  ] as const)
  const {
    data: { jwt },
  } = useAuth()
  const [loading, setLoading] = useState(false)
  const GetData = () => {
    setLoading(true)
    FileSystem.downloadAsync(
      `https://app.melandas-indonesia.com/api/v1/orders/export-quotation?type=sales_confirmation&order_id=${orderId}`,
      FileSystem.documentDirectory + `${orderData.invoiceNumber}.pdf`,
      {
        headers: {
          Authorization: `Bearer ${jwt}`,
          Accept: "application/json",
        },
      },
    )
      .then(async ({ uri }) => {
        Sharing.shareAsync(uri).then((res) => {
          // console.log(res, "quotation")
        })
      })
      .catch((err) => {
        console.log(err)
      })
      .finally(() => {
        setLoading(false)
      })
  }
  if (loading === true) {
    return <Loading />
  }

  const data: Payment[] = dataFromPaginated(paymentPaginatedData)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }
  console.log(orderData)

  return (
    <FlatList
      refreshControl={
        <RefreshControl
          colors={[COLOR_PRIMARY]}
          tintColor={COLOR_PRIMARY}
          titleColor={COLOR_PRIMARY}
          title="Loading..."
          refreshing={isManualRefetching}
          onRefresh={manualRefetch}
        />
      }
      contentContainerStyle={[{ flexGrow: 1 }, s.bgWhite]}
      data={data}
      keyExtractor={({ id }) => `paymentInfo${id}`}
      showsVerticalScrollIndicator={false}
      bounces={false}
      ListEmptyComponent={() => (
        <Text fontSize={14} textAlign="center" p={20}>
          Kosong
        </Text>
      )}
      onEndReachedThreshold={0.2}
      onEndReached={() => {
        if (hasNextPage) fetchNextPage()
      }}
      ListFooterComponent={() =>
        !!data &&
        data.length > 0 &&
        (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
      }
      renderItem={({ item, index }) => (
        <Div
          p={20}
          bg="white"
          justifyContent="space-between"
          borderBottomWidth={0.8}
          borderBottomColor={COLOR_DISABLED}
        >
          <Div mb={10} row justifyContent="space-between">
            <Text>ID:</Text>
            <Text>{item.id}</Text>
          </Div>
          <Div mb={10} row justifyContent="space-between">
            <Text>Amount:</Text>
            <Text>{formatCurrency(item.amount)}</Text>
          </Div>
          <Div mb={10} row justifyContent="space-between">
            <Text>Payment Type:</Text>
            <Text>{item.paymentType.name}</Text>
          </Div>
          <Div mb={10} row justifyContent="space-between">
            <Text>Status:</Text>
            <Tag
              containerColor={paymentStatusConfig[item.status].bg}
              textColor={paymentStatusConfig[item.status].textColor}
            >
              {paymentStatusConfig[item.status].displayText}
            </Tag>
          </Div>
          <Div mb={10} row justifyContent="space-between">
            <Text>Reference:</Text>
            <Text>{item.reference}</Text>
          </Div>

          <Button
            block
            bg="primary"
            mx={20}
            mb={10}
            alignSelf="center"
            onPress={() =>
              navigation.navigate("OrderPaymentProof", { paymentId: item.id })
            }
          >
            <Text fontWeight="bold" color="white">
              Proof of payment
            </Text>
          </Button>

          <Button
            block
            bg="primary"
            mx={20}
            mb={10}
            alignSelf="center"
            onPress={() => GetData()}
          >
            <Text fontWeight="bold" color="white">
              Sales Confirmation
            </Text>
          </Button>
        </Div>
      )}
    />
  )
}
