import { useNavigation, useRoute } from "@react-navigation/native"
import axios from "axios"
import moment from "moment"
import React, { useState } from "react"
import { FlatList, Pressable } from "react-native"
import { ScrollView } from "react-native-gesture-handler"
import { Div, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import defaultErrorHandler from "api/errors"

import { formatCurrency } from "helper"

const TotalLeadScreen = () => {
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const route = useRoute()
  const params = route.params
  const isLoading = false
  const navigation = useNavigation()
  console.warn(params.type)
  const ReportBrands = useQuery<string, any>(["TotalLeads", loggedIn], () => {
    return axios
      .get(`dashboard/report-leads/closing-deals`, {
        params: {
          start_date: moment(params?.startDate).format("YYYY-MM-DD"),
          end_date: moment(params?.endDate).format("YYYY-MM-DD"),
          supervisor_id: params.type === "SALES" ? "" : params?.id,
          channel_id: params?.channel_id,
          user_id: params.type === "SALES" ? params?.id : "",
        },
      })
      .then((res) => {
        setData(res.data)
      })
      .catch(defaultErrorHandler)
  })
  const renderTotal = ({ item }) => (
    <>
      <Pressable
        onPress={() =>
          navigation.navigate("ActivityDetail", {
            id: item.activity.id,
            isDeals: true,
          })
        }
      >
        <Div
          py={14}
          bg="#fff"
          row
          borderTopWidth={1}
          borderBottomWidth={1}
          borderColor="#c4c4c4"
          rounded={0}
          justifyContent="center"
          h={heightPercentageToDP(9)}
        >
          <Div flex={2} justifyContent="center">
            <Text
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
              fontSize={8}
            >
              {moment(item?.activity.created_at).format("YYYY-MM-DD")}
            </Text>
          </Div>
          <Div flex={2} justifyContent="center">
            <Text
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
              fontSize={8}
            >
              {!!item?.invoice_number ? item?.invoice_number : "null"}
            </Text>
          </Div>
          <Div flex={2} justifyContent="center">
            <Text
              fontSize={8}
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
            >
              {item?.name}
            </Text>
          </Div>
          <Div flex={2} justifyContent="center">
            <Text
              fontSize={8}
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
            >
              {!!formatCurrency(item?.invoice_price)
                ? formatCurrency(item?.invoice_price)
                : "0"}
            </Text>
          </Div>
          <Div flex={2} justifyContent="center">
            <Text
              fontSize={8}
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
            >
              {!!formatCurrency(item?.amount_paid)
                ? formatCurrency(item?.amount_paid)
                : "0"}
            </Text>
          </Div>
        </Div>
      </Pressable>
    </>
  )

  const headers = (
    title: string,
    title1: string,
    title2: string,
    title3: string,
    title4: string,
  ) => {
    return (
      <Div
        py={18}
        row
        bg="#17949D"
        style={{ height: heightPercentageToDP(9) }}
        justifyContent="center"
      >
        <Div flex={3} justifyContent="center">
          <Text
            color="white"
            fontWeight="bold"
            textAlign="center"
            fontSize={10}
            allowFontScaling={false}
          >
            {title}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text
            color="white"
            fontWeight="bold"
            textAlign="center"
            fontSize={10}
            allowFontScaling={false}
          >
            {title1}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text
            color="white"
            fontWeight="bold"
            textAlign="center"
            fontSize={10}
            allowFontScaling={false}
          >
            {title2}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text
            color="white"
            fontWeight="bold"
            textAlign="center"
            fontSize={10}
            allowFontScaling={false}
          >
            {title3}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text
            color="white"
            fontWeight="bold"
            textAlign="center"
            fontSize={10}
            allowFontScaling={false}
          >
            {title4}
          </Text>
        </Div>
      </Div>
    )
  }
  if (isLoading) {
    return <Loading />
  }
  return (
    <ScrollView style={{ flex: 1, backgroundColor: "#fff" }}>
      <FlatList
        bounces={false}
        data={data}
        renderItem={renderTotal}
        ListHeaderComponent={headers(
          "Dates",
          "INV",
          "Sales",
          "Invoice Price",
          "Amount Paid",
        )}
      />
    </ScrollView>
  )
}

export default TotalLeadScreen
