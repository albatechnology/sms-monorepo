import { useNavigation, useRoute } from "@react-navigation/native"
import React, { useState } from "react"
import { Dimensions, FlatList, Pressable, ScrollView } from "react-native"
import { Div, Icon, ScrollDiv, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import { formatCurrency } from "helper"

const SingleList = ({ filter }: any) => {
  const navigation = useNavigation()
  const windowHeight = Dimensions.get("screen").width
  const route = useRoute()
  const dataGet = route.params
  const isLoading = false

  const renderLeadsChannel = ({ item }) => (
    <>
      <Div
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        w={widthPercentageToDP(80)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Pressable>
            <Text
              fontWeight="bold"
              textAlign="center"
              fontSize={8}
              allowFontScaling={false}
            >
              {!!item?.total_leads ? item?.total_leads : "0"}
            </Text>
          </Pressable>
        </Div>
        <Div flex={2} justifyContent="center">
          <Pressable>
            <Text
              fontWeight="bold"
              textAlign="center"
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
      <FlatList
        data={item?.sales}
        renderItem={renderLeadsSales}
        keyExtractor={(_, idx: number) => idx.toString()}
      />
    </>
  )

  const renderLeadsSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        w={widthPercentageToDP(80)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item?.total_leads ? item?.total_leads : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item?.deal_leads ? item?.deal_leads : "0"}
          </Text>
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

  const renderHotChannel = ({ item }) => (
    <>
      <Div
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.hot_activity ? item.hot_activity : "0"}
          </Text>
        </Div>
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
      <FlatList
        data={item?.sales}
        renderItem={renderHotSales}
        keyExtractor={(_, idx: number) => idx.toString()}
      />
    </>
  )

  const renderHotSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.hot_activity ? item.hot_activity : "0"}
          </Text>
        </Div>
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

  const renderStatusChannel = ({ item }) => (
    <>
      <Div
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.closed_activity ? item.closed_activity : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.cold_activity ? item.cold_activity : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.warm_activity ? item.warm_activity : "0"}
          </Text>
        </Div>
      </Div>
      <FlatList
        data={item?.sales}
        renderItem={renderStatusSales}
        keyExtractor={(_, idx: number) => idx.toString()}
      />
    </>
  )

  const renderStatusSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.closed_activity ? item.closed_activity : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.cold_activity ? item.cold_activity : "0"}
          </Text>
        </Div>
        <Div flex={2} justifyContent="center">
          <Text
            fontWeight="bold"
            textAlign="center"
            allowFontScaling={false}
            fontSize={8}
          >
            {!!item.warm_activity ? item.warm_activity : "0"}
          </Text>
        </Div>
      </Div>
    </>
  )

  const renderSingleChannel = ({ item }) => (
    <>
      <Div
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={3} justifyContent="center">
          <Pressable
            onPress={() => navigation.navigate("SingleChannelList", item)}
          >
            <Text
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
              numberOfLines={2}
            >
              {item?.name}
            </Text>
          </Pressable>
        </Div>
      </Div>
      <FlatList
        bounces={false}
        data={item?.sales}
        renderItem={renderSingleSales}
        keyExtractor={(_, idx: number) => idx.toString()}
      />
    </>
  )

  const renderSingleSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        h={heightPercentageToDP(9)}
        justifyContent="center"
      >
        <Div flex={3} justifyContent="center">
          <Pressable
            onPress={() => navigation.navigate("SingleSalesList", item)}
          >
            <Text
              fontWeight="normal"
              textAlign="center"
              allowFontScaling={false}
              numberOfLines={2}
            >
              {item?.name}
            </Text>
          </Pressable>
        </Div>
      </Div>
    </>
  )

  const SingleListData = () => (
    <Div row>
      <Div>
        <>
          <Div>
            <Div
              // mt={38.5}
              py={18}
              row
              bg="#17949D"
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
          <Div
            py={14}
            bg="rgba(137, 189, 255, 0.7)"
            row
            borderBottomWidth={1}
            borderColor="#c4c4c4"
            rounded={0}
            h={heightPercentageToDP(9)}
          >
            <Pressable>
              <Div flex={3}>
                <Text
                  fontWeight="normal"
                  textAlign="center"
                  w={widthPercentageToDP(20)}
                  fontSize={12}
                  allowFontScaling={false}
                  numberOfLines={2}
                >
                  {dataGet?.name}
                </Text>
              </Div>
            </Pressable>
          </Div>

          <FlatList
            bounces={false}
            data={dataGet?.channels}
            renderItem={renderSingleChannel}
            keyExtractor={(_, idx: number) => idx.toString()}
          />
        </>
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
              width: widthPercentageToDP(80),
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
          <>
            <Div
              py={18}
              row
              bg="#20B5C0"
              style={{
                height: heightPercentageToDP(9),
                width: widthPercentageToDP(80),
              }}
            >
              <Div flex={2} justifyContent="center">
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
              <Div flex={2} justifyContent="center">
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
                  fontSize={13}
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
                  fontSize={13}
                  allowFontScaling={false}
                >
                  Amount Paid
                </Text>
              </Div>
            </Div>
            <Div
              py={14}
              bg="rgba(137, 189, 255, 0.7)"
              row
              borderBottomWidth={1}
              borderColor="#c4c4c4"
              rounded={0}
              h={heightPercentageToDP(9)}
              w={widthPercentageToDP(80)}
              justifyContent="center"
            >
              <Div flex={2} justifyContent="center">
                <Pressable>
                  <Text
                    fontWeight="bold"
                    textAlign="center"
                    fontSize={8}
                    allowFontScaling={false}
                  >
                    {!!dataGet?.total_leads ? dataGet?.total_leads : "0"}
                  </Text>
                </Pressable>
              </Div>

              <Div flex={2} justifyContent="center">
                <Pressable>
                  <Text
                    fontWeight="bold"
                    textAlign="center"
                    fontSize={8}
                    allowFontScaling={false}
                  >
                    {!!dataGet?.deal_leads ? dataGet?.deal_leads : "0"}
                  </Text>
                </Pressable>
              </Div>
              <Div flex={3} justifyContent="center">
                <Text
                  fontWeight="normal"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!formatCurrency(dataGet?.invoice_price)
                    ? formatCurrency(dataGet?.invoice_price)
                    : "0"}
                </Text>
              </Div>
              <Div flex={3} justifyContent="center">
                <Text
                  fontWeight="normal"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!formatCurrency(dataGet?.amount_paid)
                    ? formatCurrency(dataGet?.amount_paid)
                    : "0"}
                </Text>
              </Div>
            </Div>
            <FlatList
              bounces={false}
              data={dataGet?.channels}
              renderItem={renderLeadsChannel}
              keyExtractor={(_, idx: number) => idx.toString()}
            />
          </>
        </Div>

        <Div flex={1}>
          <Div
            // py={18}
            row
            bg="#20B5C0"
            justifyContent="center"
            style={{
              width: widthPercentageToDP(80),
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
          <>
            <Div
              py={18}
              row
              bg="#20B5C0"
              style={{
                height: heightPercentageToDP(9),
                width: widthPercentageToDP(80),
              }}
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
                  No of Leads
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
            <Div
              py={14}
              bg="rgba(137, 189, 255, 0.7)"
              row
              borderBottomWidth={1}
              borderColor="#c4c4c4"
              rounded={0}
              h={heightPercentageToDP(9)}
              w={widthPercentageToDP(80)}
              justifyContent="center"
            >
              <Div flex={2} justifyContent="center">
                <Text
                  fontWeight="bold"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!dataGet.hot_activity ? dataGet.hot_activity : "0"}
                </Text>
              </Div>
              <Div flex={2} justifyContent="center">
                <Text
                  fontWeight="normal"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!formatCurrency(dataGet.estimated_value)
                    ? formatCurrency(dataGet.estimated_value)
                    : "0"}
                </Text>
              </Div>
              <Div flex={2} justifyContent="center">
                <Text
                  fontWeight="normal"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!formatCurrency(dataGet.quotation)
                    ? formatCurrency(dataGet.quotation)
                    : "0"}
                </Text>
              </Div>
            </Div>
            <FlatList
              bounces={false}
              data={dataGet?.channels}
              renderItem={renderHotChannel}
              keyExtractor={(_, idx: number) => idx.toString()}
            />
          </>
        </Div>

        <Div flex={1}>
          <Div
            // py={18}
            row
            bg="#20B5C0"
            justifyContent="center"
            style={{
              width: widthPercentageToDP(80),
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
              Status
            </Text>
          </Div>
          <>
            <Div
              py={18}
              row
              justifyContent="center"
              bg="#20B5C0"
              style={{
                height: heightPercentageToDP(9),
                width: widthPercentageToDP(80),
              }}
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
            <Div
              py={14}
              bg="rgba(137, 189, 255, 0.7)"
              row
              borderBottomWidth={1}
              borderColor="#c4c4c4"
              rounded={0}
              h={heightPercentageToDP(9)}
              w={widthPercentageToDP(80)}
              justifyContent="center"
            >
              <Div flex={2} justifyContent="center">
                <Text
                  fontWeight="bold"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!dataGet.closed_activity ? dataGet.closed_activity : "0"}
                </Text>
              </Div>
              <Div flex={2} justifyContent="center">
                <Text
                  fontWeight="bold"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!dataGet.cold_activity ? dataGet.cold_activity : "0"}
                </Text>
              </Div>
              <Div flex={2} justifyContent="center">
                <Text
                  fontWeight="bold"
                  textAlign="center"
                  fontSize={8}
                  allowFontScaling={false}
                >
                  {!!dataGet.warm_activity ? dataGet.warm_activity : "0"}
                </Text>
              </Div>
            </Div>
            <FlatList
              bounces={false}
              data={dataGet?.channels}
              renderItem={renderStatusChannel}
            />
          </>
        </Div>
      </ScrollView>
    </Div>
  )
  if (isLoading) {
    return <Loading />
  }
  return (
    <ScrollView
      style={{ flex: 1, backgroundColor: "#fff", height: windowHeight }}
    >
      <SingleListData />
    </ScrollView>
  )
}

export default SingleList
