import { useNavigation } from "@react-navigation/native"
import React, { useState } from "react"
import {
  ActivityIndicator,
  Dimensions,
  FlatList,
  Pressable,
  RefreshControl,
  SafeAreaView,
  ScrollView,
  TouchableOpacity,
} from "react-native"
import { Div, ScrollDiv, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import { formatCurrency } from "helper"

const AllScreenMap = ({ reportData, filter, userData }: any) => {
  const navigation = useNavigation()
  const windowHeight = Dimensions.get("screen").height
  const [refreshing, setRefreshing] = useState(false)
  const renderLeadsChannel = ({ item }) => (
    <>
      <Div h={heightPercentageToDP(2)} bg="rgba(23,148,157, 0.5)" />
      <Div
        flex={1}
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
      >
        <Div flex={3} justifyContent="center" w={widthPercentageToDP(9)}>
          <Pressable
            onPress={() =>
              navigation.navigate("TotalLeadsScreen", {
                // id: item.id,
                startDate: filter?.filterUserId,
                endDate: filter?.filterCustomerHasActivity,
                channel_id: item.id,
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
        <Div flex={3} justifyContent="center" w={widthPercentageToDP(9)}>
          <Pressable
            onPress={() =>
              navigation.navigate("TotalNoOfLeads", {
                // id: item.id,
                startDate: filter?.filterUserId,
                endDate: filter?.filterCustomerHasActivity,
                channel_id: item?.id,
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
      <FlatList
        data={item?.sales}
        windowSize={5}
        maxToRenderPerBatch={5}
        removeClippedSubviews={true}
        // style={{height: heightPercentageToDP(100)}}
        renderItem={renderLeadsSales}
        keyExtractor={(_, idx: number) => idx.toString()}
      />
    </>
  )

  const renderLeadsSales = ({ item }) => (
    <>
      <Div
        // flex={1}
        py={14}
        bg="white"
        row
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
      >
        <Div flex={2} justifyContent="center" w={widthPercentageToDP(9)}>
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
        <Div flex={2} justifyContent="center" w={widthPercentageToDP(9)}>
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

  const renderHotChannel = ({ item }) => (
    <>
      <Div h={heightPercentageToDP(2)} bg="rgba(23,148,157, 0.5)" />
      <Div
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
      >
        <Div flex={2} justifyContent="center" w={widthPercentageToDP(9)}>
          <Pressable
            onPress={() =>
              navigation.navigate("TotalHotScreen", {
                // id: item.id,
                filter,
                channel_id: item.id,
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
              {!!item.hot_activity ? item.hot_activity : "0"}
            </Text>
          </Pressable>
        </Div>
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("TotalEstimated", {
                // id: item.id,
                filter,
                channel_id: item.id,
              })
            }
          >
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
          </Pressable>
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
        windowSize={5}
        maxToRenderPerBatch={5}
        removeClippedSubviews={true}
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
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
      >
        <Div flex={2} justifyContent="center">
          <Pressable
            onPress={() =>
              navigation.navigate("TotalHotScreen", {
                id: item.id,
                filter,
                channel_id: item.id,
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
              {!!item.hot_activity ? item.hot_activity : "0"}
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
              {!!formatCurrency(item.estimated_value)
                ? formatCurrency(item.estimated_value)
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
      <Div h={heightPercentageToDP(2)} bg="rgba(23,148,157, 0.5)" />
      <Div
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
      >
        <Div flex={2} justifyContent="center">
          <TouchableOpacity
            onPress={() =>
              navigation.navigate("StatusPipeline", {
                // id: item.id,
                status: "CLOSED",
                channel_id: item.id,
                filter,
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
              {!!item.drop_leads ? item.drop_leads : "0"}
            </Text>
          </TouchableOpacity>
        </Div>
        <Div flex={2} justifyContent="center">
          <TouchableOpacity
            onPress={() =>
              navigation.navigate("StatusPipeline", {
                // id: item.id,
                status: "COLD",
                channel_id: item.id,
                filter,
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
              {!!item.cold_activity ? item.cold_activity : "0"}
            </Text>
          </TouchableOpacity>
        </Div>
        <Div flex={2} justifyContent="center">
          <TouchableOpacity
            onPress={() =>
              navigation.navigate("StatusPipeline", {
                // id: item.id,
                status: "WARM",
                channel_id: item.id,
                filter,
              })
            }
          >
            <Text
              fontWeight="bold"
              color="#21B5C1"
              textAlign="center"
              fontSize={8}
              allowFontScaling={false}
            >
              {!!item.warm_activity ? item.warm_activity : "0"}
            </Text>
          </TouchableOpacity>
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
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
      >
        <Div flex={2} justifyContent="center">
          <TouchableOpacity
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
              color="#21B5C1"
              fontSize={8}
              allowFontScaling={false}
            >
              {!!item.drop_leads ? item.drop_leads : "0"}
            </Text>
          </TouchableOpacity>
        </Div>
        <Div flex={2} justifyContent="center">
          <TouchableOpacity
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
              color="#21B5C1"
              fontSize={8}
              allowFontScaling={false}
            >
              {!!item.cold_activity ? item.cold_activity : "0"}
            </Text>
          </TouchableOpacity>
        </Div>
        <Div flex={2} justifyContent="center">
          <TouchableOpacity
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
              color="#21B5C1"
              fontSize={8}
              allowFontScaling={false}
            >
              {!!item.warm_activity ? item.warm_activity : "0"}
            </Text>
          </TouchableOpacity>
        </Div>
      </Div>
    </>
  )

  const renderSingleChannel = ({ item }) => (
    <>
      <Div
        h={heightPercentageToDP(2)}
        bg="rgba(23,148,157, 0.5)"
        w={widthPercentageToDP(30)}
      />
      <Div
        py={14}
        bg="rgba(137, 189, 255, 0.3)"
        row
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
        w={widthPercentageToDP(30)}
      >
        <Pressable
          onPress={() => navigation.navigate("SingleChannelList", item)}
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
      <FlatList
        data={item?.sales}
        bounces={false}
        windowSize={5}
        maxToRenderPerBatch={5}
        removeClippedSubviews={true}
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
        borderTopWidth={1}
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
        justifyContent="center"
        h={heightPercentageToDP(10)}
        w={widthPercentageToDP(30)}
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

  const headers = (
    title: string,
    title1: string,
    title2: string,
    title3: string,
  ) => {
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

  const wait = (timeout) => {
    return new Promise((resolve) => setTimeout(resolve, timeout))
  }

  const onRefresh = React.useCallback(() => {
    setRefreshing(true)
    wait(2000).then(() => setRefreshing(false))
  }, [])

  return (
    <ScrollView
      style={{
        height: windowHeight,
        flex: 1,
        // backgroundColor: "red",
      }}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      <Div row>
        <Div>
          <Div
            py={18}
            row
            bg="#17949D"
            opacity={50}
            style={{ height: heightPercentageToDP(12) }}
          >
            <Div flex={3} justifyContent="center">
              <Text
                fontSize={18}
                color="white"
                fontWeight="bold"
                textAlign="center"
              >
                Name
              </Text>
            </Div>
          </Div>
          {reportData.map((item) => {
            return (
              <ScrollView>
                <Div>
                  <Div
                    py={14}
                    bg="rgba(137, 189, 255, 0.7)"
                    row
                    borderTopWidth={1}
                    borderBottomWidth={1}
                    borderColor="#c4c4c4"
                    rounded={0}
                    justifyContent="center"
                    h={heightPercentageToDP(10)}
                    w={widthPercentageToDP(30)}
                  >
                    <Pressable
                      onPress={() => navigation.navigate("SingleList", item)}
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
                  <FlatList
                    bounces={false}
                    windowSize={5}
                    maxToRenderPerBatch={5}
                    removeClippedSubviews={true}
                    data={item?.channels}
                    renderItem={renderSingleChannel}
                  />
                </Div>
              </ScrollView>
            )
          })}
        </Div>

        <ScrollView horizontal pagingEnabled bounces={false}>
          <SafeAreaView>
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
            <Div>
              <Div
                py={18}
                row
                bg="#20B5C0"
                style={{
                  height: heightPercentageToDP(9),
                  width: widthPercentageToDP(70),
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
              {reportData.map((item) => {
                return (
                  <
                    // style={{paddingVertical: 14, backgroundColor:"rgba(137, 189, 255, 0.3)", borderTopWidth: 1, borderBottomWidth: 1, borderColor: "#c4c4c4", justifyContent: 'center', height: heightPercentageToDP(10), width: widthPercentageToDP(30), }}
                  >
                    <Div
                      py={14}
                      bg="rgba(137, 189, 255, 0.7)"
                      borderBottomWidth={1}
                      row
                      borderColor="#c4c4c4"
                      rounded={0}
                      h={heightPercentageToDP(10)}
                      w={widthPercentageToDP(70)}
                      justifyContent="center"
                    >
                      <Div flex={2} justifyContent="center">
                        <Pressable
                          onPress={() =>
                            navigation.navigate("TotalLeadsScreen", {
                              id: item.id,
                              type: userData.type,
                              startDate: filter?.filterUserId,
                              endDate: filter?.filterCustomerHasActivity,
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
                              id: item.id,
                              type: userData.type,
                              startDate: filter?.filterUserId,
                              endDate: filter?.filterCustomerHasActivity,
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
                          fontSize={8}
                          allowFontScaling={false}
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
                          fontSize={8}
                          allowFontScaling={false}
                        >
                          {!!formatCurrency(item?.amount_paid)
                            ? formatCurrency(item?.amount_paid)
                            : "0"}
                        </Text>
                      </Div>
                    </Div>
                    <FlatList
                      bounces={false}
                      data={item?.channels}
                      renderItem={renderLeadsChannel}
                      windowSize={10}
                      maxToRenderPerBatch={10}
                      removeClippedSubviews={true}
                      keyExtractor={(_, idx: number) => idx.toString()}
                    />
                  </>
                )
              })}
            </Div>
          </SafeAreaView>

          <SafeAreaView style={{ flex: 1 }}>
            <Div
              // py={18}
              row
              bg="#20B5C0"
              justifyContent="center"
              style={{
                width: widthPercentageToDP(70),
                height: heightPercentageToDP(4),
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
            <SafeAreaView style={{ flex: 0 }}>
              <Div
                py={18}
                row
                bg="#20B5C0"
                style={{
                  height: heightPercentageToDP(8),
                  width: widthPercentageToDP(70),
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
              {reportData.map((item) => {
                return (
                  <>
                    {/* <Div h={heightPercentageToDP(2)} bg="rgba(23,148,157, 0.5)" /> */}
                    <Div
                      py={14}
                      bg="rgba(137, 189, 255, 0.7)"
                      row
                      borderBottomWidth={1}
                      borderColor="#c4c4c4"
                      rounded={0}
                      h={heightPercentageToDP(10)}
                      w={widthPercentageToDP(70)}
                      justifyContent="center"
                    >
                      <Div flex={2} justifyContent="center">
                        <Pressable
                          onPress={() =>
                            navigation.navigate("TotalHotScreen", {
                              id: item.id,
                              type: userData.type,
                              filter,
                            })
                          }
                        >
                          <Text
                            fontWeight="bold"
                            textAlign="center"
                            fontSize={8}
                            color="#21B5C1"
                            allowFontScaling={false}
                          >
                            {!!item.hot_activity ? item.hot_activity : "0"}
                          </Text>
                        </Pressable>
                      </Div>
                      <Div flex={2} justifyContent="center">
                        <Pressable
                          onPress={() =>
                            navigation.navigate("TotalEstimated", {
                              id: item.id,
                              type: userData.type,
                              filter,
                            })
                          }
                        >
                          <Text
                            fontWeight="normal"
                            textAlign="center"
                            fontSize={8}
                            allowFontScaling={false}
                          >
                            {!!formatCurrency(item.estimated_value)
                              ? formatCurrency(item.estimated_value)
                              : "0"}
                          </Text>
                        </Pressable>
                      </Div>
                      <Div flex={2} justifyContent="center">
                        <Text
                          fontWeight="normal"
                          textAlign="center"
                          fontSize={8}
                          allowFontScaling={false}
                        >
                          {!!formatCurrency(item.quotation)
                            ? formatCurrency(item.quotation)
                            : "0"}
                        </Text>
                      </Div>
                    </Div>
                    <FlatList
                      bounces={false}
                      data={item?.channels}
                      windowSize={5}
                      maxToRenderPerBatch={5}
                      removeClippedSubviews={true}
                      renderItem={renderHotChannel}
                      keyExtractor={(_, idx: number) => idx.toString()}
                    />
                  </>
                )
              })}
            </SafeAreaView>
          </SafeAreaView>

          <SafeAreaView style={{ flex: 1 }}>
            <Div
              // py={18}
              row
              bg="#20B5C0"
              justifyContent="center"
              style={{
                width: widthPercentageToDP(70),
                height: heightPercentageToDP(4),
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
            <SafeAreaView style={{ flex: 0 }}>
              <Div
                py={18}
                row
                bg="#20B5C0"
                style={{
                  height: heightPercentageToDP(8),
                  width: widthPercentageToDP(70),
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
                  <>
                    {/* <Div h={heightPercentageToDP(2)} bg="rgba(23,148,157, 0.5)" /> */}
                    <Div
                      py={14}
                      bg="rgba(137, 189, 255, 0.7)"
                      row
                      borderBottomWidth={1}
                      borderColor="#c4c4c4"
                      rounded={0}
                      h={heightPercentageToDP(10)}
                      w={widthPercentageToDP(70)}
                      justifyContent="center"
                    >
                      <Div flex={2} justifyContent="center">
                        <Pressable
                          onPress={() =>
                            navigation.navigate("StatusPipeline", {
                              id: item.id,
                              type: userData.type,
                              status: "CLOSED",
                              filter,
                            })
                          }
                        >
                          <Text
                            fontWeight="bold"
                            textAlign="center"
                            fontSize={8}
                            color="#21B5C1"
                            allowFontScaling={false}
                          >
                            {!!item.drop_leads ? item.drop_leads : "0"}
                          </Text>
                        </Pressable>
                      </Div>
                      <Div flex={2} justifyContent="center">
                        <Pressable
                          onPress={() =>
                            navigation.navigate("StatusPipeline", {
                              id: item.id,
                              type: userData.type,
                              status: "COLD",
                              filter,
                            })
                          }
                        >
                          <Text
                            fontWeight="bold"
                            textAlign="center"
                            fontSize={8}
                            color="#21B5C1"
                            allowFontScaling={false}
                          >
                            {!!item.cold_activity ? item.cold_activity : "0"}
                          </Text>
                        </Pressable>
                      </Div>
                      <Div flex={2} justifyContent="center">
                        <Pressable
                          onPress={() =>
                            navigation.navigate("StatusPipeline", {
                              id: item.id,
                              type: userData.type,
                              status: "WARM",
                              filter,
                            })
                          }
                        >
                          <Text
                            fontWeight="bold"
                            textAlign="center"
                            fontSize={8}
                            color="#21B5C1"
                            allowFontScaling={false}
                          >
                            {!!item.warm_activity ? item.warm_activity : "0"}
                          </Text>
                        </Pressable>
                      </Div>
                    </Div>
                    <FlatList
                      bounces={false}
                      data={item?.channels}
                      windowSize={5}
                      maxToRenderPerBatch={5}
                      removeClippedSubviews={true}
                      renderItem={renderStatusChannel}
                      keyExtractor={(_, idx: number) => idx.toString()}
                    />
                  </>
                )
              })}
            </SafeAreaView>
          </SafeAreaView>
        </ScrollView>
      </Div>
    </ScrollView>
  )
}

export default AllScreenMap
