import { useRoute } from "@react-navigation/native"
import axios from "axios"
import moment from "moment"
import React, { useState } from "react"
import { FlatList } from "react-native"
import { ScrollView } from "react-native-gesture-handler"
import { Div, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import defaultErrorHandler from "api/errors"

import { formatCurrency } from "helper"

const TotalChannelScreen = () => {
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const route = useRoute()
  const datas = route.params
  const isLoading = false

  const ReportBrands = useQuery<string, any>(["TotalChannel", loggedIn], () => {
    return axios
      .get(`dashboard/report-leads/closing-deals`, {
        params: {
          start_date: moment(datas?.startDate).format("YYYY-MM-DD"),
          end_date: moment(datas?.endDate).format("YYYY-MM-DD"),
          channel_id: datas?.id,
        },
      })
      .then((res) => {
        setData(res.data)
      })
      .catch(defaultErrorHandler)
      .finally(() => {})
  })

  const renderTotal = ({ item }) => (
    <>
      {/* <Div h={heightPercentageToDP(2)} bg="#F9F7F7"/> */}
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
            fontSize={8}
            allowFontScaling={false}
          >
            {item?.invoice_number}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="normal"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {item?.name}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="normal"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!formatCurrency(item?.invoice_price)
              ? formatCurrency(item?.invoice_price)
              : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="normal"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!formatCurrency(item?.amount_paid)
              ? formatCurrency(item?.amount_paid)
              : "0"}
          </Text>
        </Div>
      </Div>
    </>
  )

  const headers = (
    title: string,
    title1: string,
    title2: string,
    title3: string,
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
          "INV",
          "Sales",
          "Invoice Price",
          "Amount Paid",
        )}
      />
    </ScrollView>
  )
}

export default TotalChannelScreen
