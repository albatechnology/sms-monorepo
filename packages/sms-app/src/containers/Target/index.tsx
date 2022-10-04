/* eslint-disable custom-rules/api-error-loading-handling */
import { useNavigation } from "@react-navigation/native"
import { LinearGradient } from "expo-linear-gradient"
import moment from "moment"
import React, { useState } from "react"
import {
  FlatList,
  Pressable,
  RefreshControl,
  ScrollView,
  TouchableOpacity,
} from "react-native"
import {
  Button,
  Div,
  Icon,
  Modal,
  Skeleton,
  Text,
  Tooltip,
} from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import BotSection from "containers/Dashboard/BotSection"
import TopSection from "containers/Dashboard/TopSection"

import DatePickerInput from "components/DatePickerInput"
import SelectChannel from "components/SelectChannel"

import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import useChannelDefault from "api/hooks/channel/useChannelDefault"
import useTarget from "api/hooks/target/useTarget"
import useSuperstarList from "api/hooks/topSales/useSuperstarList"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { formatCurrency, responsive } from "helper"
import { COLOR_PRIMARY } from "helper/theme"

const TargetScreen = () => {
  const tooltipRef = React.createRef(),
    tipDeal = React.createRef(),
    tipLead = React.createRef(),
    tipActiveLead = React.createRef(),
    tipLeadStatus = React.createRef(),
    tipID = React.createRef(),
    tipSettlement = React.createRef(),
    tipQuotation = React.createRef(),
    tipEstimated = React.createRef()
  const [modalVisible, setModalVisible] = useState(false)
  const [filterVisible, setFilterVisible] = useState(false)
  const [date, setDate] = useState<any>()
  const [start, setStart] = useState<any>()
  const [end, setEnd] = useState<any>()
  const [company, setCompany] = useState<string>("")
  const [channel, setChannel] = useState<string>("")
  const {
    queries: [{ data: userData }],
  } = useMultipleQueries([useUserLoggedInData()] as const)
  const { onLogout } = useAuth()
  const defaultStart = !!start
    ? moment(start).startOf("month").format("YYYY-MM-DD")
    : moment().startOf("month").format("YYYY-MM-DD")
  const defaultEnd = !!end
    ? moment(end).endOf("month").format("YYYY-MM-DD")
    : moment().endOf("month").format("YYYY-MM-DD")

  const {
    queries: [{ data: channelData }, { data: topSalesData }, { data: target }],
    meta: { isLoading, isFetching, refetch },
  } = useMultipleQueries([
    useChannelDefault(),
    useSuperstarList("target", "yahaha", defaultStart, defaultEnd),
    useTarget({
      start_date: !!start ? moment(start).format("YYYY-MM-DD") : "",
      end_date: !!end ? moment(end).format("YYYY-MM-DD") : "",
      company_id: company,
      channel_id: channel,
    }),
  ] as const)
  const data = target?.data
  const navigation = useNavigation()
  const status = [
    {
      status: "Hot",
      total: data?.follow_up?.hot_activities,
      color: "#F44336",
    },
    {
      status: "Warm",
      total: data?.follow_up?.warm_activities,
      color: "#FFD13D",
    },
    {
      status: "Cold",
      total: data?.follow_up?.cold_activities,
      color: "#0553B7",
    },
  ]

  const FilterTarget = () => {
    return (
      <>
        {userData?.type === "SALES" ? (
          <Div row m={10} alignItems="center">
            <Div flex={1} mr={10}>
              <DatePickerInput
                placeholder="Start Date"
                value={date}
                reset={false}
                onSelect={(val) => setDate(val)}
              />
            </Div>
            <Div flex={1}>
              <DatePickerInput
                placeholder="End Date"
                value={end}
                reset={false}
                onSelect={(val) => {
                  setStart(date)
                  setEnd(val)
                }}
              />
            </Div>
          </Div>
        ) : userData?.type === "SUPERVISOR" ? (
          <Div row m={10} alignItems="center">
            <Div flex={1} mr={10}>
              <DatePickerInput
                placeholder="Start Date"
                value={date}
                reset={false}
                onSelect={(val) => setDate(val)}
              />
            </Div>
            <Div flex={1}>
              <DatePickerInput
                placeholder="End Date"
                value={end}
                reset={false}
                onSelect={(val) => {
                  setStart(date)
                  setEnd(val)
                }}
              />
            </Div>
            <TouchableOpacity
              style={{
                marginLeft: 10,
                padding: 5,
                borderRadius: 8,
                backgroundColor: !!filterVisible ? "#17949D" : "",
                alignItems: "center",
                justifyContent: "center",
              }}
              onPress={() => setFilterVisible(true)}
            >
              <Icon name="filter" fontFamily="Ionicons" fontSize={30} />
            </TouchableOpacity>
          </Div>
        ) : userData?.type === "DIRECTOR" ? (
          <Div row m={10} alignItems="center">
            <Div flex={1} mr={10}>
              <DatePickerInput
                placeholder="Start Date"
                value={date}
                reset={false}
                onSelect={(val) => setDate(val)}
              />
            </Div>
            <Div flex={1}>
              <DatePickerInput
                placeholder="End Date"
                value={end}
                reset={false}
                onSelect={(val) => {
                  setStart(date)
                  setEnd(val)
                }}
              />
            </Div>
            <TouchableOpacity
              style={{
                marginLeft: 10,
                padding: 5,
                borderRadius: 8,
                backgroundColor: !!filterVisible ? "#17949D" : "",
                alignItems: "center",
                justifyContent: "center",
              }}
              onPress={() => setFilterVisible(true)}
            >
              <Icon name="filter" fontFamily="Ionicons" fontSize={30} />
            </TouchableOpacity>
          </Div>
        ) : null}
      </>
    )
  }
  const renderStatus = ({ item }) => (
    <Div
      alignItems="center"
      row
      h={heightPercentageToDP(5)}
      justifyContent="space-between"
      borderBottomWidth={1}
      borderColor="#D9D9D9"
    >
      <Div row justifyContent="center" alignItems="center">
        <Div mx={8} h={8} w={8} rounded={8 / 2} bg={item.color} />
        <Text allowFontScaling={false} color="white">
          {item.status}
        </Text>
      </Div>
      <Text allowFontScaling={false} color="white">
        {item.total}
      </Text>
    </Div>
  )
  const Header = () => (
    <>
      {/* Deals */}
      {userData.type === "SALES" ? (
        <Pressable
          onPress={() =>
            navigation.navigate("QuotationInside", {
              type: userData?.type,
              id: userData?.id,
              name: userData?.name,
              invoice_type: "deals",
              startDate: !!start ? start : moment().startOf("month"),
              endDate: !!end ? end : moment().endOf("month"),
            })
          }
        >
          <Div
            row
            justifyContent="space-between"
            rounded={4}
            p={8}
            mx={10}
            h={heightPercentageToDP(14)}
            bg="#17949D"
          >
            <Div>
              <Div row alignItems="center" mt={10}>
                <Text allowFontScaling={false} fontSize={12} color="white">
                  Deals
                </Text>
                <TouchableOpacity
                  onPress={() => {
                    if (tipDeal.current) {
                      tipDeal.current.show()
                    }
                  }}
                >
                  <Icon
                    ml={5}
                    name="info"
                    color="grey"
                    fontFamily="Feather"
                    fontSize={12}
                  />
                </TouchableOpacity>
                <Tooltip
                  ref={tipDeal}
                  mr={widthPercentageToDP(10)}
                  text={`Jumlah total pencapaian anda`}
                />
              </Div>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={16}
                  fontWeight="bold"
                  color="white"
                >
                  {isLoading === true ? (
                    <Skeleton.Box
                      w={widthPercentageToDP(20)}
                      h={heightPercentageToDP(3)}
                    />
                  ) : (
                    formatCurrency(data?.deals?.value)
                  )}
                </Text>
                <Icon
                  ml={3}
                  name={
                    data?.deals?.value < data?.deals?.compare
                      ? "caretdown"
                      : "caretup"
                  }
                  fontFamily="AntDesign"
                  fontSize={8}
                  color={
                    data?.deals?.value < data?.deals?.compare
                      ? "#F44336"
                      : "#2DCC70"
                  }
                />
              </Div>
              <Text allowFontScaling={false} fontSize={12} my={8} color="white">
                Target{" "}
                {isLoading === true ? (
                  <Skeleton.Box
                    h={heightPercentageToDP(1)}
                    w={widthPercentageToDP(40)}
                  />
                ) : (
                  formatCurrency(data?.deals?.target_deals)
                )}
              </Text>
            </Div>
            <Div justifyContent="center">
              <Progress.Circle
                style={{}}
                unfilledColor="#005F66"
                borderWidth={0}
                size={75}
                progress={
                  data?.deals?.value / data?.deals?.target_deals === Infinity ||
                  isNaN(data?.deals?.value / data?.deals?.target_deals)
                    ? 0
                    : data?.deals?.value / data?.deals?.target_deals
                }
                animated={false}
                thickness={12}
                showsText={true}
                color={"white"}
              />
            </Div>
          </Div>
        </Pressable>
      ) : (
        <Div
          row
          justifyContent="space-between"
          rounded={4}
          p={8}
          mx={10}
          h={heightPercentageToDP(14)}
          bg="#17949D"
        >
          <Div>
            <Div row alignItems="center" mt={10}>
              <Text allowFontScaling={false} fontSize={12} color="white">
                Deals
              </Text>
              <TouchableOpacity
                onPress={() => {
                  if (tipDeal.current) {
                    tipDeal.current.show()
                  }
                }}
              >
                <Icon
                  ml={5}
                  name="info"
                  color="grey"
                  fontFamily="Feather"
                  fontSize={12}
                />
              </TouchableOpacity>
              <Tooltip
                ref={tipDeal}
                mr={widthPercentageToDP(10)}
                text={`Jumlah total pencapaian anda`}
              />
            </Div>
            <Div row>
              <Text
                allowFontScaling={false}
                fontSize={16}
                fontWeight="bold"
                color="white"
              >
                {isLoading === true ? (
                  <Skeleton.Box
                    w={widthPercentageToDP(20)}
                    h={heightPercentageToDP(3)}
                  />
                ) : (
                  formatCurrency(data?.deals?.value)
                )}
              </Text>
              <Icon
                ml={3}
                name={
                  data?.deals?.value < data?.deals?.compare
                    ? "caretdown"
                    : "caretup"
                }
                fontFamily="AntDesign"
                fontSize={8}
                color={
                  data?.deals?.value < data?.deals?.compare
                    ? "#F44336"
                    : "#2DCC70"
                }
              />
            </Div>
            <Text allowFontScaling={false} fontSize={12} my={8} color="white">
              Target{" "}
              {isLoading === true ? (
                <Skeleton.Box
                  h={heightPercentageToDP(1)}
                  w={widthPercentageToDP(40)}
                />
              ) : (
                formatCurrency(data?.deals?.target_deals)
              )}
            </Text>
          </Div>
          <Div justifyContent="center">
            <Progress.Circle
              style={{}}
              unfilledColor="#005F66"
              borderWidth={0}
              size={75}
              progress={
                data?.deals?.value / data?.deals?.target_deals === Infinity ||
                isNaN(data?.deals?.value / data?.deals?.target_deals)
                  ? 0
                  : data?.deals?.value / data?.deals?.target_deals
              }
              animated={false}
              thickness={12}
              showsText={true}
              color={"white"}
            />
          </Div>
        </Div>
      )}

      {/* Leads */}
      <Div row mx={10} mt={8} justifyContent="space-around">
        {userData.type === "SALES" ? (
          <Div>
            <Pressable
              onPress={() =>
                navigation.navigate("SalesNewLeads", {
                  type: userData?.type,
                  id: userData?.id,
                  name: userData?.name,
                  startDate: !!start ? start : moment().startOf("month"),
                  endDate: !!end ? end : moment().endOf("month"),
                  isActive: 0,
                })
              }
            >
              <Div>
                <Div
                  rounded={4}
                  p={10}
                  w={widthPercentageToDP(31)}
                  h={heightPercentageToDP(11)}
                  bg="#179D63"
                >
                  <Div row>
                    <Text allowFontScaling={false} fontSize={10} color="white">
                      New Leads
                    </Text>
                    <TouchableOpacity
                      onPress={() => {
                        if (tipLead.current) {
                          tipLead.current.show()
                        }
                      }}
                    >
                      <Icon
                        ml={5}
                        name="info"
                        color="grey"
                        fontFamily="Feather"
                        fontSize={12}
                      />
                    </TouchableOpacity>
                    <Tooltip
                      ref={tipLead}
                      mr={widthPercentageToDP(10)}
                      text={`Jumlah lead baru anda pada bulan ini`}
                    />
                  </Div>
                  <Div row>
                    <Text
                      allowFontScaling={false}
                      fontSize={12}
                      fontWeight="bold"
                      color="white"
                    >
                      {isLoading === true ? (
                        <Skeleton.Box
                          h={heightPercentageToDP(2.5)}
                          w={widthPercentageToDP(10)}
                        />
                      ) : (
                        data?.new_leads?.value
                      )}
                    </Text>
                    <Icon
                      ml={3}
                      name={
                        data?.new_leads?.value < data?.new_leads?.compare
                          ? "caretdown"
                          : "caretup"
                      }
                      fontFamily="AntDesign"
                      fontSize={8}
                      color={
                        data?.new_leads?.value < data?.new_leads?.compare
                          ? "#F44336"
                          : "#2DCC70"
                      }
                    />
                  </Div>
                  <Div row>
                    <Div>
                      <Progress.Bar
                        borderRadius={0}
                        color="#FFFFFF"
                        borderWidth={0}
                        height={3}
                        useNativeDriver
                        unfilledColor="#c4c4c4"
                        width={widthPercentageToDP(20)}
                        style={{ marginTop: 5 }}
                        progress={
                          data?.new_leads?.value /
                            data?.new_leads?.target_leads ===
                            Infinity ||
                          isNaN(
                            data?.new_leads?.value /
                              data?.new_leads?.target_leads,
                          )
                            ? 0
                            : data?.new_leads?.value /
                              data?.new_leads?.target_leads
                        }
                      />
                    </Div>
                    <Text
                      fontSize={responsive(8)}
                      color="#fff"
                      ml={heightPercentageToDP(1)}
                    >
                      {`${Math.round(
                        (data?.new_leads?.value /
                          data?.new_leads?.target_leads) *
                          100,
                      )}%`}
                    </Text>
                  </Div>
                  <Text fontSize={responsive(10)} color="white">
                    Target {data?.new_leads?.target_leads}
                  </Text>
                </Div>
              </Div>
            </Pressable>
            <Pressable
              onPress={() =>
                navigation.navigate("SalesNewLeads", {
                  type: userData?.type,
                  id: userData?.id,
                  name: userData?.name,
                  startDate: !!start ? start : moment().startOf("month"),
                  endDate: !!end ? end : moment().endOf("month"),
                  isActive: 1,
                })
              }
            >
              <Div
                rounded={4}
                p={10}
                w={widthPercentageToDP(31)}
                h={heightPercentageToDP(8.5)}
                mt={heightPercentageToDP(1)}
                bg="#4FD69B"
              >
                <Div>
                  <Div row>
                    <Text allowFontScaling={false} fontSize={10} color="white">
                      Active Leads
                    </Text>
                    <TouchableOpacity
                      onPress={() => {
                        if (tipActiveLead.current) {
                          tipActiveLead.current.show()
                        }
                      }}
                    >
                      <Icon
                        ml={5}
                        name="info"
                        color="grey"
                        fontFamily="Feather"
                        fontSize={12}
                      />
                    </TouchableOpacity>
                    <Tooltip
                      ref={tipActiveLead}
                      mr={widthPercentageToDP(10)}
                      text={`Jumlah total pencapaian anda`}
                    />
                  </Div>
                  <Text allowFontScaling={false} fontSize={10} color="white">
                    {data?.active_leads?.value}
                  </Text>
                </Div>
              </Div>
            </Pressable>
          </Div>
        ) : (
          <Div>
            <Div
              rounded={4}
              p={10}
              w={widthPercentageToDP(31)}
              h={heightPercentageToDP(11)}
              bg="#179D63"
            >
              <Div row>
                <Text allowFontScaling={false} fontSize={10} color="white">
                  New Leads
                </Text>
                <TouchableOpacity
                  onPress={() => {
                    if (tipLead.current) {
                      tipLead.current.show()
                    }
                  }}
                >
                  <Icon
                    ml={5}
                    name="info"
                    color="grey"
                    fontFamily="Feather"
                    fontSize={12}
                  />
                </TouchableOpacity>
                <Tooltip
                  ref={tipLead}
                  mr={widthPercentageToDP(10)}
                  text={`Jumlah lead baru anda pada bulan ini`}
                />
              </Div>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={12}
                  fontWeight="bold"
                  color="white"
                >
                  {isLoading === true ? (
                    <Skeleton.Box
                      h={heightPercentageToDP(2.5)}
                      w={widthPercentageToDP(10)}
                    />
                  ) : (
                    data?.new_leads?.value
                  )}
                </Text>
                <Icon
                  ml={3}
                  name={
                    data?.new_leads?.value < data?.new_leads?.compare
                      ? "caretdown"
                      : "caretup"
                  }
                  fontFamily="AntDesign"
                  fontSize={8}
                  color={
                    data?.new_leads?.value < data?.new_leads?.compare
                      ? "#F44336"
                      : "#2DCC70"
                  }
                />
              </Div>
              <Div row>
                <Div>
                  <Progress.Bar
                    borderRadius={0}
                    color="#FFFFFF"
                    borderWidth={0}
                    height={3}
                    useNativeDriver
                    unfilledColor="#c4c4c4"
                    width={widthPercentageToDP(20)}
                    style={{ marginTop: 5 }}
                    progress={
                      data?.new_leads?.value / data?.new_leads?.target_leads ===
                        Infinity ||
                      isNaN(
                        data?.new_leads?.value / data?.new_leads?.target_leads,
                      )
                        ? 0
                        : data?.new_leads?.value / data?.new_leads?.target_leads
                    }
                  />
                </Div>
                <Text
                  fontSize={responsive(8)}
                  color="#fff"
                  ml={heightPercentageToDP(1)}
                >
                  {`${Math.round(
                    (data?.new_leads?.value / data?.new_leads?.target_leads) *
                      100,
                  )}%`}
                </Text>
              </Div>
              <Text fontSize={responsive(10)} color="white">
                Target {data?.new_leads?.target_leads}
              </Text>
            </Div>
            <Div
              rounded={4}
              p={10}
              w={widthPercentageToDP(31)}
              h={heightPercentageToDP(8.5)}
              mt={heightPercentageToDP(1)}
              bg="#4FD69B"
            >
              <Div>
                <Div row>
                  <Text allowFontScaling={false} fontSize={10} color="white">
                    Active Leads
                  </Text>
                  <TouchableOpacity
                    onPress={() => {
                      if (tipActiveLead.current) {
                        tipActiveLead.current.show()
                      }
                    }}
                  >
                    <Icon
                      ml={5}
                      name="info"
                      color="#c4c4c4"
                      fontFamily="Feather"
                      fontSize={12}
                    />
                  </TouchableOpacity>
                  <Tooltip
                    ref={tipActiveLead}
                    mr={widthPercentageToDP(10)}
                    text={`Jumlah total active lead`}
                  />
                </Div>
                <Text
                  allowFontScaling={false}
                  fontSize={12}
                  fontWeight="bold"
                  color="white"
                >
                  {data?.active_leads?.value}
                </Text>
              </Div>
            </Div>
          </Div>
        )}

        {userData.type === "SALES" ? (
          <Pressable
            onPress={() =>
              navigation.navigate("InteriorDesignInside", {
                type: userData?.type,
                id: userData?.id,
                name: userData?.name,
                startDate: !!start ? start : moment().startOf("month"),
                endDate: !!end ? end : moment().endOf("month"),
              })
            }
          >
            <Div
              w={widthPercentageToDP(30)}
              rounded={4}
              px={5}
              h={heightPercentageToDP(20.5)}
              mb={5}
              bg="#F7AD97"
            >
              <Div row mt={heightPercentageToDP(2.5)}>
                <Text allowFontScaling={false} fontSize={10} color="white">
                  Interior Design
                </Text>
                <TouchableOpacity
                  onPress={() => {
                    if (tipID.current) {
                      tipID.current.show()
                    }
                  }}
                >
                  <Icon
                    ml={5}
                    name="info"
                    color="lightgrey"
                    fontFamily="Feather"
                    fontSize={12}
                  />
                </TouchableOpacity>
                <Tooltip
                  ref={tipID}
                  mr={widthPercentageToDP(10)}
                  text={`Jumlah total penjualan melalui interior design`}
                />
              </Div>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={12}
                  fontWeight="bold"
                  color="white"
                >
                  {isLoading === true ? (
                    <Skeleton.Box
                      h={heightPercentageToDP(2.5)}
                      w={widthPercentageToDP(10)}
                    />
                  ) : (
                    formatCurrency(data?.interior_design?.value)
                  )}
                </Text>
              </Div>
              <Div borderTopWidth={1} borderColor="white" my={2} py={5}>
                <Text
                  allowFontScaling={false}
                  fontSize={10}
                  color="white"
                >{`Transaction\n${data?.interior_design?.total_transaction}`}</Text>
              </Div>
            </Div>
          </Pressable>
        ) : (
          <Div
            w={widthPercentageToDP(30)}
            rounded={4}
            px={5}
            h={heightPercentageToDP(20.5)}
            mb={5}
            bg="#F7AD97"
          >
            <Div row mt={heightPercentageToDP(2.5)}>
              <Text allowFontScaling={false} fontSize={10} color="white">
                Interior Design
              </Text>
              <TouchableOpacity
                onPress={() => {
                  if (tipID.current) {
                    tipID.current.show()
                  }
                }}
              >
                <Icon
                  ml={5}
                  name="info"
                  color="lightgrey"
                  fontFamily="Feather"
                  fontSize={12}
                />
              </TouchableOpacity>
              <Tooltip
                ref={tipID}
                mr={widthPercentageToDP(10)}
                text={`Jumlah total penjualan melalui interior design`}
              />
            </Div>
            <Div row>
              <Text
                allowFontScaling={false}
                fontSize={12}
                fontWeight="bold"
                color="white"
              >
                {isLoading === true ? (
                  <Skeleton.Box
                    h={heightPercentageToDP(2.5)}
                    w={widthPercentageToDP(10)}
                  />
                ) : (
                  formatCurrency(data?.interior_design?.value)
                )}
              </Text>
            </Div>
            <Div borderTopWidth={1} borderColor="white" my={2} py={5}>
              <Text
                allowFontScaling={false}
                fontSize={10}
                color="white"
              >{`Transaction\n${data?.interior_design?.total_transaction}`}</Text>
            </Div>
          </Div>
        )}
        {userData.type === "SALES" ? (
          <Pressable
            onPress={() =>
              navigation.navigate("QuotationInside", {
                type: userData?.type,
                id: userData?.id,
                name: userData?.name,
                invoice_type: "retail",
                startDate: !!start ? start : moment().startOf("month"),
                endDate: !!end ? end : moment().endOf("month"),
              })
            }
          >
            <Div
              w={widthPercentageToDP(30)}
              rounded={4}
              px={5}
              h={heightPercentageToDP(20.5)}
              mb={5}
              bg="#F5907B"
            >
              <Div row mt={heightPercentageToDP(2.5)}>
                <Text allowFontScaling={false} fontSize={10} color="white">
                  Retails
                </Text>
                <TouchableOpacity
                  onPress={() => {
                    if (tipSettlement.current) {
                      tipSettlement.current.show()
                    }
                  }}
                >
                  <Icon
                    ml={5}
                    name="info"
                    color="lightgrey"
                    fontFamily="Feather"
                    fontSize={12}
                  />
                </TouchableOpacity>
                <Tooltip
                  ref={tipSettlement}
                  ml={widthPercentageToDP(10)}
                  text={`Jumlah total pencapaian retail anda`}
                />
              </Div>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={12}
                  fontWeight="bold"
                  color="white"
                >
                  {isLoading === true ? (
                    <Skeleton.Box
                      h={heightPercentageToDP(2.5)}
                      w={widthPercentageToDP(10)}
                    />
                  ) : (
                    formatCurrency(data?.retail?.value)
                  )}
                </Text>
              </Div>
              <Div borderTopWidth={1} borderColor="white" my={2} py={5}>
                <Text
                  allowFontScaling={false}
                  fontSize={10}
                  color="white"
                >{`Transaction\n${data?.retail?.total_transaction}`}</Text>
              </Div>
            </Div>
          </Pressable>
        ) : (
          <Div
            w={widthPercentageToDP(30)}
            rounded={4}
            px={5}
            h={heightPercentageToDP(20.5)}
            mb={5}
            bg="#F5907B"
          >
            <Div row mt={heightPercentageToDP(2.5)}>
              <Text allowFontScaling={false} fontSize={10} color="white">
                Retails
              </Text>
              <TouchableOpacity
                onPress={() => {
                  if (tipSettlement.current) {
                    tipSettlement.current.show()
                  }
                }}
              >
                <Icon
                  ml={5}
                  name="info"
                  color="lightgrey"
                  fontFamily="Feather"
                  fontSize={12}
                />
              </TouchableOpacity>
              <Tooltip
                ref={tipSettlement}
                ml={widthPercentageToDP(10)}
                text={`Jumlah total pencapaian retail anda`}
              />
            </Div>
            <Div row>
              <Text
                allowFontScaling={false}
                fontSize={12}
                fontWeight="bold"
                color="white"
              >
                {isLoading === true ? (
                  <Skeleton.Box
                    h={heightPercentageToDP(2.5)}
                    w={widthPercentageToDP(10)}
                  />
                ) : (
                  formatCurrency(data?.retail?.value)
                )}
              </Text>
            </Div>
            <Div borderTopWidth={1} borderColor="white" my={2} py={5}>
              <Text
                allowFontScaling={false}
                fontSize={10}
                color="white"
              >{`Transaction\n${data?.retail?.total_transaction}`}</Text>
            </Div>
          </Div>
        )}
      </Div>

      <Div mx={10} p={8} mt={5} bg="#17949D" rounded={6}>
        <Div row>
          <Text
            allowFontScaling={false}
            fontSize={responsive(10)}
            color="white"
          >
            Follow Up
          </Text>
          <TouchableOpacity
            onPress={() => {
              if (tooltipRef.current) {
                tooltipRef.current.show()
              }
            }}
          >
            <Icon
              ml={5}
              name="info"
              color="grey"
              fontFamily="Feather"
              fontSize={12}
            />
          </TouchableOpacity>
          <Tooltip
            ref={tooltipRef}
            mr={widthPercentageToDP(10)}
            text={`Jumlah Follow up yang dilakukan ke customer`}
          />
        </Div>
        <Div row alignItems="center">
          <Text
            allowFontScaling={false}
            fontSize={responsive(12)}
            my={5}
            fontWeight="bold"
            color="white"
          >
            {isLoading === true ? (
              <Skeleton.Box
                h={heightPercentageToDP(2.5)}
                w={widthPercentageToDP(10)}
              />
            ) : (
              data?.follow_up?.total_activities?.value
            )}
          </Text>
          <Icon
            ml={5}
            name={
              data?.follow_up?.total_activities?.value <
              data?.follow_up?.total_activities?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              data?.follow_up?.total_activities?.value <
              data?.follow_up?.total_activities?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Progress.Bar
          borderRadius={0}
          progress={
            data?.follow_up?.total_activities?.value /
              data?.follow_up?.total_activities?.target_activities ===
              Infinity ||
            isNaN(
              data?.follow_up?.total_activities?.value /
                data?.follow_up?.total_activities?.target_activities,
            )
              ? 0
              : data?.follow_up?.total_activities?.value /
                data?.follow_up?.total_activities?.target_activities
          }
          color="#FFFFFF"
          borderWidth={0}
          height={5}
          useNativeDriver
          unfilledColor="#c4c4c4"
          width={widthPercentageToDP("90%")}
        />
        <Text my={5} fontSize={responsive(8)} color="#c4c4c4">
          Target {data?.follow_up?.total_activities?.target_activities}{" "}
          {`(${Math.round(
            (data?.follow_up?.total_activities?.value /
              data?.follow_up?.total_activities?.target_activities) *
              100,
          )}%)`}
        </Text>
      </Div>

      <Div mx={10} p={8} mt={5} bg="#20B5C0" rounded={6}>
        {userData?.type === "SALES" ? (
          <Div>
            <Div row>
              <Text
                allowFontScaling={false}
                fontSize={responsive(10)}
                color="white"
              >
                Lead Status
              </Text>
              <TouchableOpacity
                onPress={() => {
                  if (tipLeadStatus.current) {
                    tipLeadStatus.current.show()
                  }
                }}
              >
                <Icon
                  ml={5}
                  name="info"
                  color="grey"
                  fontFamily="Feather"
                  fontSize={12}
                />
              </TouchableOpacity>
              <Tooltip
                ref={tipLeadStatus}
                mr={widthPercentageToDP(10)}
                text={`Jumlah Leads berdasarkan status COLD, WARM, dan HOT`}
              />
            </Div>
            <FlatList
              data={status}
              renderItem={({ item }) => (
                <Pressable
                  onPress={() =>
                    navigation.navigate("FollowTarget", {
                      type: userData?.type?.toLowerCase(),
                      id: userData?.id,
                      name: userData?.name,
                      status: item?.status,
                      startDate: !!start ? start : moment().startOf("month"),
                      endDate: !!end ? end : moment().endOf("month"),
                    })
                  }
                >
                  <Div
                    row
                    alignItems="center"
                    h={heightPercentageToDP(5)}
                    justifyContent="space-between"
                    borderTopWidth={1}
                    borderColor="#D9D9D9"
                  >
                    <Div row justifyContent="center" alignItems="center">
                      <Div
                        mx={8}
                        h={8}
                        w={8}
                        rounded={8 / 2}
                        bg={item?.color}
                      />
                      <Text allowFontScaling={false} color="#fff">
                        {item?.status}
                      </Text>
                    </Div>
                    <Text allowFontScaling={false} color="#fff">
                      {" "}
                      {item?.total}
                    </Text>
                  </Div>
                </Pressable>
              )}
            />
          </Div>
        ) : (
          <Div>
            <Div row>
              <Text
                allowFontScaling={false}
                fontSize={responsive(10)}
                color="white"
              >
                Lead Status
              </Text>
              <TouchableOpacity
                onPress={() => {
                  if (tipLeadStatus.current) {
                    tipLeadStatus.current.show()
                  }
                }}
              >
                <Icon
                  ml={5}
                  name="info"
                  color="grey"
                  fontFamily="Feather"
                  fontSize={12}
                />
              </TouchableOpacity>
              <Tooltip
                ref={tipLeadStatus}
                mr={widthPercentageToDP(10)}
                text={`Jumlah Leads berdasarkan status COLD, WARM, dan HOT`}
              />
            </Div>
            <FlatList data={status} renderItem={renderStatus} />
          </Div>
        )}
      </Div>

      <Div row mx={15} mt={10} justifyContent="center">
        {userData.type === "SALES" ? (
          <Pressable
            onPress={() =>
              navigation.navigate("QuotationInside", {
                type: userData?.type,
                id: userData?.id,
                name: userData?.name,
                invoice_type: "quotation",
                startDate: !!start ? start : moment().startOf("month"),
                endDate: !!end ? end : moment().endOf("month"),
              })
            }
          >
            <Div
              w={widthPercentageToDP(46)}
              rounded={4}
              px={5}
              mx={5}
              h={heightPercentageToDP(11)}
              bg="#17519D"
              justifyContent="center"
            >
              <Div row>
                <Text allowFontScaling={false} fontSize={10} color="white">
                  Quotation
                </Text>
                <TouchableOpacity
                  onPress={() => {
                    if (tipQuotation.current) {
                      tipQuotation.current.show()
                    }
                  }}
                >
                  <Icon
                    ml={5}
                    name="info"
                    color="grey"
                    fontFamily="Feather"
                    fontSize={12}
                  />
                </TouchableOpacity>
                <Tooltip
                  ref={tipQuotation}
                  mr={widthPercentageToDP(10)}
                  text={`Jumlah nominal quotation yang sudah dibuat`}
                />
              </Div>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={12}
                  fontWeight="bold"
                  color="white"
                >
                  {isLoading === true ? (
                    <Skeleton.Box
                      h={heightPercentageToDP(2.5)}
                      w={widthPercentageToDP(40)}
                    />
                  ) : (
                    formatCurrency(data?.quotation?.value)
                  )}
                </Text>
                <Icon
                  ml={3}
                  name={
                    data?.quotation?.value < data?.quotation?.compare
                      ? "caretdown"
                      : "caretup"
                  }
                  fontFamily="AntDesign"
                  fontSize={8}
                  color={
                    data?.quotation?.value < data?.quotation?.compare
                      ? "#F44336"
                      : "#2DCC70"
                  }
                />
              </Div>
              {/* <Progress.Bar
              borderRadius={0}
              progress={0.6}
              color="#FFFFFF"
              borderWidth={0}
              height={3}
              useNativeDriver
              unfilledColor="#c4c4c4"
              width={widthPercentageToDP(40)}
              style={{ marginBottom: 5 }}
            />
            <Text fontSize={10} color="white">
              Target {formatCurrency(950000)}
            </Text> */}
            </Div>
          </Pressable>
        ) : (
          <Div
            w={widthPercentageToDP(46)}
            rounded={4}
            px={5}
            mx={5}
            h={heightPercentageToDP(11)}
            bg="#17519D"
            justifyContent="center"
          >
            <Div row>
              <Text allowFontScaling={false} fontSize={10} color="white">
                Quotation
              </Text>
              <TouchableOpacity
                onPress={() => {
                  if (tipQuotation.current) {
                    tipQuotation.current.show()
                  }
                }}
              >
                <Icon
                  ml={5}
                  name="info"
                  color="grey"
                  fontFamily="Feather"
                  fontSize={12}
                />
              </TouchableOpacity>
              <Tooltip
                ref={tipQuotation}
                mr={widthPercentageToDP(10)}
                text={`Jumlah nominal quotation yang sudah dibuat`}
              />
            </Div>
            <Div row>
              <Text
                allowFontScaling={false}
                fontSize={12}
                fontWeight="bold"
                color="white"
              >
                {isLoading === true ? (
                  <Skeleton.Box
                    h={heightPercentageToDP(2.5)}
                    w={widthPercentageToDP(40)}
                  />
                ) : (
                  formatCurrency(data?.quotation?.value)
                )}
              </Text>
              <Icon
                ml={3}
                name={
                  data?.quotation?.value < data?.quotation?.compare
                    ? "caretdown"
                    : "caretup"
                }
                fontFamily="AntDesign"
                fontSize={8}
                color={
                  data?.quotation?.value < data?.quotation?.compare
                    ? "#F44336"
                    : "#2DCC70"
                }
              />
            </Div>
            {/* <Progress.Bar
              borderRadius={0}
              progress={0.6}
              color="#FFFFFF"
              borderWidth={0}
              height={3}
              useNativeDriver
              unfilledColor="#c4c4c4"
              width={widthPercentageToDP(40)}
              style={{ marginBottom: 5 }}
            />
            <Text fontSize={10} color="white">
              Target {formatCurrency(950000)}
            </Text> */}
          </Div>
        )}
        {userData.type === "SALES" ? (
          <Pressable
            onPress={() =>
              navigation.navigate("EstimatedInside", {
                type: userData?.type,
                id: userData?.id,
                name: userData?.name,
                startDate: !!start ? start : moment().startOf("month"),
                endDate: !!end ? end : moment().endOf("month"),
              })
            }
          >
            <Div
              w={widthPercentageToDP(46)}
              rounded={4}
              px={5}
              mx={5}
              h={heightPercentageToDP(11)}
              bg="#3F82D9"
              justifyContent="center"
            >
              <Div row>
                <Text allowFontScaling={false} fontSize={10} color="white">
                  Pipelines
                </Text>
                <TouchableOpacity
                  onPress={() => {
                    if (tipEstimated.current) {
                      tipEstimated.current.show()
                    }
                  }}
                >
                  <Icon
                    ml={5}
                    name="info"
                    color="grey"
                    fontFamily="Feather"
                    fontSize={12}
                  />
                </TouchableOpacity>
                <Tooltip
                  ref={tipEstimated}
                  mr={widthPercentageToDP(10)}
                  text={`Jumlah nominal estimasi yang diinput pada saat setiap follow up dibuat`}
                />
              </Div>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={12}
                  fontWeight="bold"
                  color="white"
                >
                  {isLoading === true ? (
                    <Skeleton.Box
                      h={heightPercentageToDP(2.5)}
                      w={widthPercentageToDP(40)}
                    />
                  ) : (
                    formatCurrency(data?.estimation?.value)
                  )}
                </Text>
                <Icon
                  ml={3}
                  name={
                    data?.estimation?.value < data?.estimation?.compare
                      ? "caretdown"
                      : "caretup"
                  }
                  fontFamily="AntDesign"
                  fontSize={8}
                  color={
                    data?.estimation?.value < data?.estimation?.compare
                      ? "#F44336"
                      : "#2DCC70"
                  }
                />
              </Div>
              {/* <Progress.Bar
            borderRadius={0}
            progress={0.6}
            color="#FFFFFF"
            borderWidth={0}
            height={3}
            useNativeDriver
            unfilledColor="#c4c4c4"
            width={widthPercentageToDP(40)}
            style={{ marginBottom: 5 }}
          />
          <Text fontSize={10} color="white">
            Target {formatCurrency(950000)}
          </Text> */}
            </Div>
          </Pressable>
        ) : (
          <Div
            w={widthPercentageToDP(46)}
            rounded={4}
            px={5}
            mx={5}
            h={heightPercentageToDP(11)}
            bg="#3F82D9"
            justifyContent="center"
          >
            <Div row>
              <Text allowFontScaling={false} fontSize={10} color="white">
                Pipelines
              </Text>
              <TouchableOpacity
                onPress={() => {
                  if (tipEstimated.current) {
                    tipEstimated.current.show()
                  }
                }}
              >
                <Icon
                  ml={5}
                  name="info"
                  color="grey"
                  fontFamily="Feather"
                  fontSize={12}
                />
              </TouchableOpacity>
              <Tooltip
                ref={tipEstimated}
                mr={widthPercentageToDP(10)}
                text={`Jumlah nominal estimasi yang diinput pada saat setiap follow up dibuat`}
              />
            </Div>
            <Div row>
              <Text
                allowFontScaling={false}
                fontSize={12}
                fontWeight="bold"
                color="white"
              >
                {isLoading === true ? (
                  <Skeleton.Box
                    h={heightPercentageToDP(2.5)}
                    w={widthPercentageToDP(40)}
                  />
                ) : (
                  formatCurrency(data?.estimation?.value)
                )}
              </Text>
              <Icon
                ml={3}
                name={
                  data?.estimation?.value < data?.estimation?.compare
                    ? "caretdown"
                    : "caretup"
                }
                fontFamily="AntDesign"
                fontSize={8}
                color={
                  data?.estimation?.value < data?.estimation?.compare
                    ? "#F44336"
                    : "#2DCC70"
                }
              />
            </Div>
            {/* <Progress.Bar
            borderRadius={0}
            progress={0.6}
            color="#FFFFFF"
            borderWidth={0}
            height={3}
            useNativeDriver
            unfilledColor="#c4c4c4"
            width={widthPercentageToDP(40)}
            style={{ marginBottom: 5 }}
          />
          <Text fontSize={10} color="white">
            Target {formatCurrency(950000)}
          </Text> */}
          </Div>
        )}
      </Div>
    </>
  )
  const [tempCompany, setTempCompany] = useState<any>("")
  const [tempChannel, setTempChannel] = useState<any>("")
  const FilterBase = () => (
    <Modal
      isVisible={filterVisible}
      h={heightPercentageToDP(50)}
      roundedTop={6}
      onBackdropPress={() => setFilterVisible(false)}
    >
      <Text ml={widthPercentageToDP(6)} mt={10} fontSize={responsive(12)}>
        Filter Base
      </Text>
      {userData?.type === "SUPERVISOR" ? (
        <>
          <Div mx={20}>
            <SelectChannel
              value={channel}
              title="Status"
              message="Please select a channel"
              onSelect={(value) => {
                setChannel(value)
                setFilterVisible(false)
              }}
              id={tempCompany}
            />
          </Div>
          <TouchableOpacity
            onPress={() => {
              setCompany("")
              setChannel("")
              setFilterVisible(false)
            }}
          >
            <LinearGradient
              style={{
                height: 40,
                justifyContent: "center",
                borderRadius: 4,
                width: widthPercentageToDP(30),
                marginLeft: widthPercentageToDP(7),
                marginTop: heightPercentageToDP(5),
              }}
              locations={[0.5, 1.0]}
              colors={["#20B5C0", "#17949D"]}
            >
              <Text
                allowFontScaling={false}
                color="white"
                fontSize={14}
                textAlign="center"
              >
                Reset
              </Text>
            </LinearGradient>
          </TouchableOpacity>
        </>
      ) : (
        <>
          <Div row p={20}>
            <Button
              bg="white"
              onPress={() => setCompany("1")}
              borderWidth={1}
              borderColor={company === "1" ? "#17949D" : "grey"}
              color={company === "1" ? "#17949D" : "grey"}
              mr={10}
            >
              Melandas
            </Button>
            <Button
              bg="white"
              onPress={() => setCompany("2")}
              borderWidth={1}
              borderColor={company === "2" ? "#17949D" : "grey"}
              color={company === "2" ? "#17949D" : "grey"}
            >
              Dio Living
            </Button>
          </Div>
          <Div mx={20}>
            <SelectChannel
              value={channel}
              title="Status"
              message="Please select a channel"
              onSelect={(value) => {
                setChannel(value)
                setFilterVisible(false)
              }}
              id={tempCompany}
            />
          </Div>
          <TouchableOpacity
            onPress={() => {
              setCompany("")
              setChannel("")
              setFilterVisible(false)
            }}
          >
            <LinearGradient
              style={{
                height: 40,
                justifyContent: "center",
                borderRadius: 4,
                width: widthPercentageToDP(30),
                marginLeft: widthPercentageToDP(7),
                marginTop: heightPercentageToDP(5),
              }}
              locations={[0.5, 1.0]}
              colors={["#20B5C0", "#17949D"]}
            >
              <Text
                allowFontScaling={false}
                color="white"
                fontSize={14}
                textAlign="center"
              >
                Reset
              </Text>
            </LinearGradient>
          </TouchableOpacity>
        </>
      )}
    </Modal>
  )
  // Modal
  const Comparison = () => (
    <Modal
      isVisible={modalVisible}
      h={heightPercentageToDP(60)}
      roundedTop={6}
      onBackdropPress={() => setModalVisible(false)}
    >
      <Div row justifyContent="space-between" p={20}>
        <Text
          allowFontScaling={false}
          fontWeight="bold"
          fontSize={responsive(14)}
        >
          Comparison
        </Text>
        <Button
          bg="#c4c4c4"
          h={35}
          w={35}
          rounded="circle"
          onPress={() => {
            setModalVisible(false)
          }}
        >
          <Icon color="black" name="close" />
        </Button>
      </Div>
      <Div
        row
        justifyContent="space-around"
        alignItems="center"
        borderColor="#c4c4c4"
        borderBottomWidth={1}
      >
        <Div row flex={3}>
          <Text
            textAlign="left"
            ml={heightPercentageToDP(2)}
            allowFontScaling={false}
            fontSize={responsive(12)}
          >
            Detail
          </Text>
        </Div>
        <Div flex={3}>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(data?.compare_date?.start).format("DD MMM YYYY")}
          </Text>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(data?.compare_date?.end).format("DD MMM YYYY")}
          </Text>
        </Div>
        <Div flex={3}>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(data?.original_date?.start).format("DD MMM YYYY")}
          </Text>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(data?.original_date?.end).format("DD MMM YYYY")}
          </Text>
        </Div>
      </Div>

      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Deals
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              data?.deals?.value < data?.deals?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              data?.deals?.value < data?.deals?.compare ? "#F44336" : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(data?.deals?.compare)}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(data?.deals?.value)}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            New Leads
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              data?.new_leads?.value < data?.new_leads?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              data?.new_leads?.value < data?.new_leads?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {data?.new_leads?.compare}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {data?.new_leads?.value}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Follow Up
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              data?.follow_up?.total_activities?.value <
              data?.follow_up?.total_activities?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              data?.follow_up?.total_activities?.value <
              data?.follow_up?.total_activities?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {data?.follow_up?.total_activities?.compare}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {data?.follow_up?.total_activities?.value}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Quotation
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              data?.quotation?.value < data?.quotation?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              data?.quotation?.value < data?.quotation?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(data?.quotation?.compare)}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(data?.quotation?.value)}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Pipelines
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              data?.estimation?.value < data?.estimation?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              data?.estimation?.value < data?.estimation?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(data?.estimation?.compare)}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(data?.estimation?.value)}
        </Text>
      </Div>
    </Modal>
  )

  return (
    <ScrollView
      refreshControl={
        <RefreshControl
          colors={[COLOR_PRIMARY]}
          tintColor={COLOR_PRIMARY}
          titleColor={COLOR_PRIMARY}
          title="Loading..."
          refreshing={isFetching}
          onRefresh={refetch}
        />
      }
      showsVerticalScrollIndicator={false}
    >
      <TopSection userData={userData} channelData={channelData} />
      <FilterTarget />
      <Header />
      {userData.type === "SALES" ? (
        <>
          <Div mx={10}>
            <Button
              onPress={() => setModalVisible(true)}
              my={8}
              color="#17949D"
              fontWeight="bold"
              bg="white"
              borderWidth={1}
              borderColor="#17949D"
              rounded={6}
              w={widthPercentageToDP(95)}
              alignSelf="center"
            >
              Comparison
            </Button>
          </Div>
        </>
      ) : (
        <>
          <TouchableOpacity
            onPress={() => navigation.navigate("Target", userData)}
          >
            <LinearGradient
              style={{
                height: 40,
                justifyContent: "center",
                borderRadius: 4,
                marginTop: 8,
                marginHorizontal: widthPercentageToDP(3),
              }}
              locations={[0.5, 1.0]}
              colors={["#20B5C0", "#17949D"]}
            >
              <Text
                allowFontScaling={false}
                color="white"
                fontSize={14}
                textAlign="center"
              >
                See Detail
              </Text>
            </LinearGradient>
          </TouchableOpacity>
          <Div row justifyContent="space-between" mx={10}>
            <Button
              onPress={() => setModalVisible(true)}
              my={8}
              color="#17949D"
              fontWeight="bold"
              bg="white"
              borderWidth={1}
              borderColor="#17949D"
              rounded={6}
              w={widthPercentageToDP(46)}
              alignSelf="center"
            >
              Comparison
            </Button>
            <Button
              onPress={() =>
                navigation.navigate("EstimatedInside", {
                  type: userData?.type,
                  id: userData?.id,
                  company_id: company,
                  startDate: !!start ? start : moment().startOf("month"),
                  endDate: !!end ? end : moment().endOf("month"),
                })
              }
              my={5}
              color="#17949D"
              fontWeight="bold"
              bg="white"
              borderWidth={1}
              borderColor="#17949D"
              rounded={6}
              w={widthPercentageToDP(46)}
              alignSelf="center"
            >
              Brand
            </Button>
          </Div>
        </>
      )}
      <Div mt={10} />
      <BotSection
        userData={userData}
        data={topSalesData?.data}
        startDate={defaultStart}
        endDate={defaultEnd}
      />
      <TouchableOpacity
        onPress={() => {
          onLogout()
        }}
      >
        <LinearGradient
          style={{
            height: 40,
            justifyContent: "center",
            borderRadius: 4,
            marginHorizontal: widthPercentageToDP(2),
          }}
          locations={[0.5, 1.0]}
          colors={["#20B5C0", "#17949D"]}
        >
          <Text
            allowFontScaling={false}
            color="white"
            fontSize={14}
            textAlign="center"
          >
            Logout
          </Text>
        </LinearGradient>
      </TouchableOpacity>
      <Div>
        <Comparison />
        <FilterBase />
      </Div>
    </ScrollView>
  )
}

export default TargetScreen
