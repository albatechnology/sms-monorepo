import { useNavigation } from "@react-navigation/native"
import React, { useState } from "react"
import { Dimensions, FlatList, ScrollView, Pressable } from "react-native"
import { Div, ScrollDiv, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import { formatCurrency } from "helper"

const SalesScreen = ({ reportData, filter }: any) => {
  const navigation = useNavigation()
  const windowHeight = Dimensions.get("screen").width
  const isLoading = false

  const renderLeads = ({ item }) => (
    <>
      <Div
        py={14}
        bg="#fff"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("TotalLeadsScreen", {
                // id: item.id,
                startDate: filter?.filterUserId,
                endDate: filter?.filterCustomerHasActivity,
                // channel_id: item.id,
                user_id: item.id,
              })
            }
          >
            <Text
              fontWeight="bold"
              textAlign="center"
              color="#21B5C1"
              fontSize={8}
              allowFontScaling={false}
            >
              {!!item?.total_leads ? item?.total_leads : "0"}
            </Text>
          </Pressable>
        </Div>
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("TotalNoOfLeads", {
                // id: item.id,
                startDate: filter?.filterUserId,
                endDate: filter?.filterCustomerHasActivity,
                // channel_id: item.channel_id,
                user_id: item.id,
              })
            }
          >
            <Text
              fontWeight="bold"
              textAlign="center"
              color="#21B5C1"
              fontSize={8}
              allowFontScaling={false}
            >
              {!!item?.deal_leads ? item?.deal_leads : "0"}
            </Text>
          </Pressable>
        </Div>
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

  const renderHot = ({ item }) => (
    <>
      <Div
        py={14}
        bg="#fff"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("TotalHotScreen", {
                // id: item.id,
                filter,
                // channel_id: item.id,
                user_id: item.id,
              })
            }
          >
            <Text
              fontWeight="bold"
              textAlign="center"
              allowFontScaling={false}
              color="#21B5C1"
              fontSize={8}
            >
              {!!item?.hot_activity ? item?.hot_activity : "0"}
            </Text>
          </Pressable>
        </Div>
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("TotalEstimated", {
                // id: item.id,
                filter,
                // channel_id: item.id,
                user_id: item.id,
              })
            }
          >
            <Text
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
              fontSize={8}
            >
              {!!formatCurrency(item?.estimated_value)
                ? formatCurrency(item?.estimated_value)
                : "0"}
            </Text>
          </Pressable>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="normal"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!formatCurrency(item?.quotation)
              ? formatCurrency(item?.quotation)
              : "0"}
          </Text>
        </Div>
      </Div>
    </>
  )

  const renderStatus = ({ item }) => (
    <>
      <Div
        py={14}
        bg="#fff"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("StatusPipeline", {
                // id: item.id,
                status: "CLOSED",
                // channel_id: item.id,
                user_id: item.id,
                filter,
              })
            }
          >
            <Text
              fontWeight="bold"
              textAlign="center"
              allowFontScaling={false}
              color="#21B5C1"
              fontSize={8}
            >
              {!!item?.closed_activity ? item?.closed_activity : "0"}
            </Text>
          </Pressable>
        </Div>
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("StatusPipeline", {
                // id: item.id,
                status: "COLD",
                // channel_id: item.id,
                user_id: item.id,
                filter,
              })
            }
          >
            <Text
              fontWeight="bold"
              textAlign="center"
              allowFontScaling={false}
              color="#21B5C1"
              fontSize={8}
            >
              {!!item?.cold_activity ? item?.cold_activity : "0"}
            </Text>
          </Pressable>
        </Div>
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("StatusPipeline", {
                // id: item.id,
                status: "WARM",
                // channel_id: item.id,
                user_id: item.id,
                filter,
              })
            }
          >
            <Text
              fontWeight="bold"
              textAlign="center"
              allowFontScaling={false}
              color="#21B5C1"
              fontSize={8}
            >
              {!!item?.warm_activity ? item?.warm_activity : "0"}
            </Text>
          </Pressable>
        </Div>
      </Div>
    </>
  )

  const renderSingle = ({ item }) => (
    <>
      <Div
        py={14}
        bg="#fff"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(9)}
      >
        <Pressable
          onPress={() =>
            navigation.navigate("TotalLeadsScreen", {
              // id: item.id,
              startDate: filter?.filterUserId,
              endDate: filter?.filterCustomerHasActivity,
              // channel_id: item.channel_id,
              user_id: item.id,
            })
          }
        >
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
              numberOfLines={2}
            >
              {item?.name}
            </Text>
          </Div>
        </Pressable>
      </Div>
    </>
  )

  const SalesList = () => (
    <Div row>
      <Div>
        <Div>
          <Div
            //   mt={38.5}
            py={18}
            row
            bg="#17949D"
            opacity={50}
            style={{ height: heightPercentageToDP(13.7) }}
          >
            <Div flex={3} justifyContent="center">
              <Text
                color="white"
                fontSize={18}
                fontWeight="bold"
                textAlign="center"
                allowFontScaling={false}
              >
                Name
              </Text>
            </Div>
          </Div>
        </Div>
        {reportData.map((item) => {
          return (
            <FlatList
              style={{ width: widthPercentageToDP(30) }}
              data={item?.channels[0]?.sales}
              renderItem={renderSingle}
              // keyExtractor={(_, idx: number) => idx.toString()}
              // ListHeaderComponent={singleHeader}
            />
          )
        })}
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
            style={{
              width: widthPercentageToDP(70),
              height: heightPercentageToDP(4.69),
            }}
          >
            <Text
              textAlign="center"
              fontSize={18}
              fontWeight="bold"
              color="#fff"
              mt={heightPercentageToDP(0.5)}
              allowFontScaling={false}
            >
              Closing Deals
            </Text>
          </Div>
          <Div
            py={18}
            row
            bg="#20B5C0"
            style={{ height: heightPercentageToDP(9) }}
            justifyContent="center"
          >
            <Div flex={2} justifyContent="center" w={widthPercentageToDP(10)}>
              <Text
                color="white"
                fontWeight="bold"
                textAlign="center"
                fontSize={10}
                allowFontScaling={false}
              >
                Leads
              </Text>
            </Div>
            <Div flex={2} justifyContent="center" w={widthPercentageToDP(15)}>
              <Text
                color="white"
                fontWeight="bold"
                textAlign="center"
                fontSize={10}
                allowFontScaling={false}
              >
                No of Leads
              </Text>
            </Div>
            <Div flex={3} justifyContent="center">
              <Text
                color="white"
                fontWeight="bold"
                textAlign="center"
                fontSize={12}
                ml={heightPercentageToDP(1)}
                w={widthPercentageToDP(15)}
                allowFontScaling={false}
              >
                Invoice Price
              </Text>
            </Div>
            <Div flex={3} justifyContent="center">
              <Text
                color="white"
                fontWeight="bold"
                textAlign="center"
                fontSize={12}
                w={widthPercentageToDP(18)}
                allowFontScaling={false}
              >
                Amount Paid
              </Text>
            </Div>
          </Div>
          {reportData.map((item) => {
            return (
              <FlatList
                style={{ width: widthPercentageToDP(70) }}
                data={item?.channels[0].sales}
                renderItem={renderLeads}
                // keyExtractor={(_, idx: number) => idx.toString()}
                // ListHeaderComponent={headers(
                //   "Leads",
                //   "No of Leads",
                //   "Invoice Price",
                //   "Amount Paid",
                // )}
              />
            )
          })}
        </Div>

        <Div flex={1}>
          <Div
            // py={18}
            row
            bg="#20B5C0"
            justifyContent="center"
            style={{
              width: widthPercentageToDP(70),
              height: heightPercentageToDP(4.69),
            }}
          >
            <Text
              textAlign="center"
              fontSize={18}
              fontWeight="bold"
              color="#fff"
              mt={heightPercentageToDP(0.5)}
              allowFontScaling={false}
            >
              Hot
            </Text>
          </Div>
          <Div
            py={18}
            row
            bg="#20B5C0"
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
                No Of Leads
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
                Estimated
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
                Quotation
              </Text>
            </Div>
          </Div>
          {reportData.map((item) => {
            return (
              <FlatList
                style={{ width: widthPercentageToDP(70) }}
                data={item?.channels[0]?.sales}
                renderItem={renderHot}
                // keyExtractor={(_, idx: number) => idx.toString()}
                // ListHeaderComponent={header(
                //   "No of Leads",
                //   "Estimated",
                //   "Quotation",
                // )}
              />
            )
          })}
        </Div>

        <Div flex={1}>
          <Div
            // py={18}
            row
            bg="#20B5C0"
            justifyContent="center"
            style={{
              width: widthPercentageToDP(70),
              height: heightPercentageToDP(4.69),
            }}
          >
            <Text
              textAlign="center"
              fontSize={20}
              color="#fff"
              mt={heightPercentageToDP(0.5)}
              allowFontScaling={false}
            >
              Status
            </Text>
          </Div>
          <Div
            py={18}
            row
            bg="#20B5C0"
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
                Drop
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
                Cold
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
                Warm
              </Text>
            </Div>
          </Div>
          {reportData.map((item) => {
            return (
              <FlatList
                style={{ width: widthPercentageToDP(70) }}
                data={item?.channels[0]?.sales}
                renderItem={renderStatus}
                // keyExtractor={(_, idx: number) => idx.toString()}
                // ListHeaderComponent={header("Drop", "Cold", "Warm")}
              />
            )
          })}
        </Div>
      </ScrollView>
    </Div>
  )

  if (isLoading) {
    return <Loading />
  }
  return (
    <ScrollView
      bounces={false}
      style={{ flex: 1, backgroundColor: "#fff", height: windowHeight }}
    >
      <SalesList />
    </ScrollView>
  )
}

export default SalesScreen
