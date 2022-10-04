import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useState } from "react"
import {
  Dimensions,
  FlatList,
  Pressable,
  ScrollView,
  TouchableOpacity,
} from "react-native"
import { Div, Icon, ScrollDiv, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import defaultErrorHandler from "api/errors"

import { formatCurrency } from "helper"

const TotalLeadsScreen = () => {
  const navigation = useNavigation()
  const windowHeight = Dimensions.get("screen").height
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const route = useRoute()
  const params = route.params
  const isLoading = false
  console.log(params?.id, "user id")
  const ReportBrands = useQuery<string, any>(
    ["TotalLeadsDetails", loggedIn],
    () => {
      return axios
        .get(`dashboard/report-leads/details`, {
          params: {
            start_date: moment(params?.startDate).format("YYYY-MM-DD"),
            end_date: moment(params?.endDate).format("YYYY-MM-DD"),
            supervisor_id: params.type === "SALES" ? "" : params?.id,
            channel_id: params?.channel_id,
            user_id: params.type === "SALES" ? params?.id : "",
          },
        })
        .then((res) => {
          console.log(res, "resulktt")
          setData(res.data)
        })
        .catch(defaultErrorHandler)
    },
  )
  const renderSingleLeads = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
        row
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(9)}
      >
        <Pressable>
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              textAlign="center"
              fontSize={8}
              allowFontScaling={false}
            >
              {item?.first_name}
              {item?.last_name || ""}
            </Text>
          </Div>
        </Pressable>
      </Div>
    </>
  )

  const renderLeadsSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
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
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!formatCurrency(item?.invoice_price)
              ? formatCurrency(item?.invoice_price)
              : "0"}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
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

  const renderHotSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
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
            {!!formatCurrency(item.estimated_value)
              ? formatCurrency(item.estimated_value)
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
            {!!formatCurrency(item.quotation)
              ? formatCurrency(item.quotation)
              : "0"}
          </Text>
        </Div>
      </Div>
    </>
  )

  const renderStatusSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
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
            {!!item.cold ? item.cold : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="normal"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.warm ? item.warm : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="normal"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.hot ? item.hot : "0"}
          </Text>
        </Div>
      </Div>
    </>
  )
  const header = (title: string, title1: string) => {
    return (
      <Div
        py={20}
        row
        bg="#20B5C0"
        style={{ height: heightPercentageToDP(9.5) }}
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
      </Div>
    )
  }

  const headers = (title: string, title1: string, title2: string) => {
    return (
      <Div
        py={20}
        row
        bg="#20B5C0"
        justifyContent="center"
        style={{ height: heightPercentageToDP(9.5) }}
      >
        <Div flex={2} justifyContent="center">
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
        <Div flex={2} justifyContent="center">
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
        <Div flex={2} justifyContent="center">
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
      </Div>
    )
  }

  const SingleHeader = (title: string, title1: string, title2: string) => {
    return (
      <Div>
        <Div
          //   mt={39}
          py={18}
          row
          bg="#17949D"
          opacity={50}
          style={{ height: heightPercentageToDP(12.5) }}
        >
          <Div flex={3} justifyContent="center">
            <Text
              fontSize={18}
              color="white"
              fontWeight="bold"
              textAlign="center"
            >
              Leads
            </Text>
          </Div>
        </Div>
      </Div>
    )
  }
  if (isLoading) {
    return <Loading />
  }
  return (
    <ScrollView
      bounces={false}
      style={{ flex: 1, backgroundColor: "#fff", height: windowHeight }}
    >
      <Div row>
        <Div>
          <FlatList
            bounces={false}
            style={{ width: widthPercentageToDP(30) || "auto" }}
            data={data}
            renderItem={renderSingleLeads}
            keyExtractor={(_, idx: number) => idx.toString()}
            ListHeaderComponent={SingleHeader}
          />
        </Div>

        <ScrollView
          style={{ backgroundColor: "#fff", width: "100%" }}
          horizontal
          pagingEnabled
          showsHorizontalScrollIndicator={false}
          // scrollEventThrottle={16}
          bounces={false}
          nestedScrollEnabled
        >
          <Div>
            <Div
              // py={18}
              row
              bg="#20B5C0"
              justifyContent="center"
              alignSelf="center"
              style={{
                width: widthPercentageToDP(70),
                height: heightPercentageToDP(3),
              }}
            >
              <Text
                textAlign="center"
                fontWeight="bold"
                fontSize={14}
                color="#fff"
                mt={heightPercentageToDP(0.5)}
                allowFontScaling={false}
              >
                Closing Deals
              </Text>
            </Div>
            <FlatList
              style={{ width: widthPercentageToDP(70) }}
              data={data}
              bounces={false}
              renderItem={renderLeadsSales}
              keyExtractor={(_, idx: number) => idx.toString()}
              ListHeaderComponent={header("Invoice Price", "Amount Paid")}
            />
          </Div>

          <Div flex={1}>
            <Div
              // py={18}
              row
              bg="#20B5C0"
              justifyContent="center"
              style={{
                width: widthPercentageToDP(69),
                height: heightPercentageToDP(3),
              }}
            >
              <Text
                textAlign="center"
                fontWeight="bold"
                fontSize={14}
                color="#fff"
                mt={heightPercentageToDP(0.5)}
                allowFontScaling={false}
              >
                Hot
              </Text>
            </Div>
            <FlatList
              style={{ width: widthPercentageToDP(69) }}
              data={data}
              bounces={false}
              renderItem={renderHotSales}
              keyExtractor={(_, idx: number) => idx.toString()}
              ListHeaderComponent={header("Estimated", "Quotation")}
            />
          </Div>

          <Div flex={1}>
            <Div
              // py={18}
              row
              bg="#20B5C0"
              justifyContent="center"
              style={{
                width: widthPercentageToDP(69),
                height: heightPercentageToDP(3),
              }}
            >
              <Text
                textAlign="center"
                fontWeight="bold"
                fontSize={14}
                color="#fff"
                mt={heightPercentageToDP(0.5)}
                allowFontScaling={false}
              >
                Status
              </Text>
            </Div>
            <FlatList
              style={{ width: widthPercentageToDP(69) }}
              data={data}
              bounces={false}
              renderItem={renderStatusSales}
              keyExtractor={(_, idx: number) => idx.toString()}
              ListHeaderComponent={headers("Cold", "Warm", "Hot")}
            />
          </Div>
        </ScrollView>
      </Div>
    </ScrollView>
  )
}

export default TotalLeadsScreen
