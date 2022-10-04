import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useState } from "react"
import { FlatList, TouchableOpacity } from "react-native"
import {
  Button,
  Div,
  Icon,
  Input,
  Modal,
  ScrollDiv,
  Text,
} from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import useInvoiceTarget from "api/hooks/target/useInvoiceTarget"

import { formatCurrency, responsive } from "helper"

const dataLabel = [
  {
    id: 1,
    name: "Quotation",
    payment: "none",
  },
  {
    id: 2,
    name: "Deals",
    payment: "deals",
  },
  {
    id: 3,
    name: "Down Payment",
    payment: "down_payment",
  },
  {
    id: 4,
    name: "Settlement",
    payment: "settlement",
  },
  {
    id: 5,
    name: "Partial",
    payment: "partial",
  },
]
const QuotationInside = () => {
  const navigation = useNavigation()
  const route = useRoute()
  const [key, setKey] = useState<string>()
  const [filterType, setFilterType] = useState<string>("customer")
  const [visible, setVisible] = useState(false)
  const [indexCategory, setIndexCategory] = useState(
    route.params.invoice_type === "quotation" ? 0 : 1,
  )
  const [payment, setPayment] = useState(
    route.params.invoice_type === "quotation" ? "none" : "deals",
  )
  const {
    queries: [{ data: dataList }],
    meta: { isLoading },
  } = useMultipleQueries([
    useInvoiceTarget({
      id: route.params.id,
      user_type: route.params.type,
      start_date: moment(route.params.startDate).format("YYYY-MM-DD"),
      end_date: moment(route.params.endDate).format("YYYY-MM-DD"),
      payment_type: payment,
      invoice_type: route.params.invoice_type,
      search_type: filterType,
      name: key,
    }),
  ] as const)
  const TopSection = () => {
    return (
      <Div justifyContent="space-between" bg="primary" p={20}>
        <Div>
          <Text
            allowFontScaling={false}
            fontWeight="bold"
            fontSize={responsive(14)}
            color="white"
          >
            {route.params.name}
          </Text>
        </Div>
      </Div>
    )
  }

  const renderButton = ({ item, index }: any) => {
    return (
      <Div mx={3}>
        <Button
          w={widthPercentageToDP(30)}
          h={heightPercentageToDP(5)}
          fontSize={responsive(8)}
          onPress={() => {
            setIndexCategory(index)
            setPayment(item.payment)
          }}
          bg={item?.id === indexCategory + 1 ? "rgba(23,148,157, 0.5)" : "#fff"}
          color={item?.id === indexCategory + 1 ? "#17949D" : "#000"}
          borderColor={item?.id === indexCategory + 1 ? "#17949D" : "#c4c4c4"}
          borderWidth={1}
        >
          {item?.name}
        </Button>
      </Div>
    )
  }

  const renderItem = ({ item }) => {
    const id = item?.activity_id
    const isDeals = true
    return (
      <TouchableOpacity
        onPress={() => {
          navigation.navigate("ActivityDetail", { id, isDeals })
        }}
      >
        <Div m={10} rounded={8} bg="#fff" p={10}>
          <Div
            justifyContent="space-between"
            py={5}
            borderBottomColor="#c4c4c4"
            borderBottomWidth={1}
            row
          >
            <Div>
              <Text
                allowFontScaling={false}
                fontSize={responsive(10)}
                color="#C4C4C4"
              >
                {item?.invoice_number}
              </Text>
              <Text allowFontScaling={false} fontSize={responsive(10)}>
                {item?.created_at}
              </Text>
            </Div>

            <Div w={widthPercentageToDP(30)}>
              <Div
                borderColor="#000"
                borderWidth={1}
                p={10}
                justifyContent="center"
                alignItems="center"
              >
                <Text
                  numberOfLines={2}
                  allowFontScaling={false}
                  fontSize={responsive(8)}
                  textAlign="center"
                >
                  {item?.channel}
                </Text>
              </Div>
              <Text
                mt={heightPercentageToDP(1)}
                allowFontScaling={false}
                fontSize={responsive(8)}
                textAlign="right"
              >
                {item?.sales}
              </Text>
            </Div>
          </Div>
          <Div mt={heightPercentageToDP(1)}>
            <Text allowFontScaling={false} mb={2} fontSize={responsive(10)}>
              {item?.customer}
            </Text>
            <Text
              allowFontScaling={false}
              fontWeight="bold"
              fontSize={responsive(10)}
            >
              {formatCurrency(item?.total_price)}
            </Text>
          </Div>
          <Div h={5} />
        </Div>
      </TouchableOpacity>
    )
  }

  return (
    <ScrollDiv>
      <TopSection />
      {route.params.invoice_type === "retail" ? null : (
        <Div>
          <Div p={10} bg="#88C6CA" mb={heightPercentageToDP(1)}>
            <Div row>
              <Icon
                name="info"
                color="#313132"
                fontFamily="Feather"
                fontSize={16}
              />
              <Text ml={5}>
                {payment === "partial"
                  ? "Pembayaran belum cukup untuk dianggap sebagai Down Payment"
                  : payment === "none"
                  ? "List quotation yang belum deal"
                  : payment === "down_payment"
                  ? "Pembayaran sudah memenuhi sebagai Down Payment"
                  : payment === "deals"
                  ? "transaksi berhasil closing"
                  : "Pembayaran sudah lunas"}
              </Text>
            </Div>
          </Div>
          <FlatList
            showsHorizontalScrollIndicator={false}
            horizontal
            data={dataLabel}
            renderItem={renderButton}
            bounces={false}
            contentContainerStyle={{ marginHorizontal: 10 }}
          />
        </Div>
      )}
      <Div alignItems="center" mt={5}>
        <Input
          w={widthPercentageToDP(95)}
          placeholder={`Search `}
          value={key}
          onChangeText={(val) => setKey(val)}
          suffix={
            <Div row>
              <TouchableOpacity
                onPress={() => setVisible(!visible)}
                style={{
                  borderWidth: 1,
                  marginRight: 5,
                  padding: 8,
                  borderRadius: 4,
                  borderColor: "#17949D",
                }}
              >
                <Text color="#17949D">{filterType}</Text>
              </TouchableOpacity>
              <Icon
                name="search"
                fontSize={responsive(12)}
                color="gray900"
                fontFamily="Feather"
              />
            </Div>
          }
        />
      </Div>
      {!dataList ? (
        <Loading />
      ) : (
        <FlatList
          data={dataList?.data}
          renderItem={renderItem}
          bounces={false}
          keyExtractor={(_, idx: number) => idx.toString()}
        />
      )}
      <Modal
        isVisible={visible}
        h={heightPercentageToDP(35)}
        roundedTop={6}
        onBackdropPress={() => setVisible(!visible)}
      >
        <Div p={10}>
          <Text fontSize={responsive(10)}>Set filter by</Text>
          <TouchableOpacity
            onPress={() => {
              setFilterType("customer")
              setVisible(!visible)
            }}
            style={{
              padding: 15,
              borderBottomWidth: 1,
              borderColor: "#c4c4c4",
            }}
          >
            <Text>Customer</Text>
          </TouchableOpacity>
          <TouchableOpacity
            onPress={() => {
              setFilterType("invoice_number")
              setVisible(!visible)
            }}
            style={{
              padding: 15,
              borderBottomWidth: 1,
              borderColor: "#c4c4c4",
            }}
          >
            <Text>Invoice Number</Text>
          </TouchableOpacity>
          <TouchableOpacity
            onPress={() => {
              setFilterType("sales")
              setVisible(!visible)
            }}
            style={{
              padding: 15,
              borderBottomWidth: 1,
              borderColor: "#c4c4c4",
            }}
          >
            <Text>Sales</Text>
          </TouchableOpacity>
          <TouchableOpacity
            onPress={() => {
              setFilterType("channel")
              setVisible(!visible)
            }}
            style={{
              padding: 15,
              borderBottomWidth: 1,
              borderColor: "#c4c4c4",
            }}
          >
            <Text>Channel</Text>
          </TouchableOpacity>
        </Div>
      </Modal>
    </ScrollDiv>
  )
}

export default QuotationInside
