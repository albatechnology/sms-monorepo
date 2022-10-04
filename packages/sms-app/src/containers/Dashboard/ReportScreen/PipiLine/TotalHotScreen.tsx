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

const TotalHotScreen = () => {
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const route = useRoute()
  const params = route.params
  const isLoading = false
  const navigation = useNavigation()
  const ReportBrands = useQuery<string, any>(
    ["TotalHotLeads", loggedIn],
    () => {
      return axios
        .get(`dashboard/report-leads/hot`, {
          params: {
            start_date: moment(params?.filter?.filterUserId).format(
              "YYYY-MM-DD",
            ),
            end_date: moment(params?.filter?.filterCustomerHasActivity).format(
              "YYYY-MM-DD",
            ),
            supervisor_id: params.type === "SALES" ? "" : params?.id,
            channel_id: params?.channel_id,
            sales_id: params.type === "SALES" ? params?.id : "",
          },
        })
        .then((res) => {
          setData(res.data)
        })
        .catch(defaultErrorHandler)
    },
  )
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
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              fontSize={8}
              textAlign="center"
              allowFontScaling={false}
            >
              {moment(item?.activity?.created_at).format("YYYY-MM-DD")}
            </Text>
          </Div>
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              fontSize={8}
              textAlign="center"
              allowFontScaling={false}
            >
              {item?.customer?.first_name}
              {item?.customer?.last_name}
            </Text>
          </Div>
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              fontSize={8}
              textAlign="center"
              allowFontScaling={false}
            >
              {item?.name}
            </Text>
          </Div>
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              fontSize={8}
              textAlign="center"
              allowFontScaling={false}
            >
              {!!formatCurrency(item?.quotation)
                ? formatCurrency(item?.quotation)
                : "0"}
            </Text>
          </Div>
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              fontSize={8}
              textAlign="center"
              allowFontScaling={false}
            >
              {!!formatCurrency(item?.estimated_value)
                ? formatCurrency(item?.estimated_value)
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
          "Date",
          "Leads",
          "Sales",
          "Quotation",
          "Estimated",
        )}
      />
    </ScrollView>
  )
}

export default TotalHotScreen
