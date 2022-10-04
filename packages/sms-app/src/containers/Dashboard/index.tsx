/* eslint-disable react-hooks/exhaustive-deps */
import { RouteProp, useNavigation, useRoute } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import { LinearGradient } from "expo-linear-gradient"
import moment from "moment"
import React, { useEffect, useRef, useState } from "react"
import { RefreshControl, ScrollView, Image } from "react-native"
import { TouchableOpacity } from "react-native-gesture-handler"
import { Button, Div, Overlay, Text } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import Loading from "components/Loading"
import MonthPickerInput from "components/MonthPickerInput"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import useChannelDefault from "api/hooks/channel/useChannelDefault"
import useSuperstarList from "api/hooks/topSales/useSuperstarList"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { EntryStackParamList } from "Router/EntryStackParamList"

import BotSection from "./BotSection"
import BrandCategory from "./BrandCategory"
import FollowSection from "./FollowSection"
import InteriorDesign from "./InteriorDesign"
import Invoice from "./InvoiceManual"
import FilterSection from "./ReportScreen/FilterSection"
import SalesSection from "./ReportScreen/SalesSection"
import SettlementCount from "./SettlementCount"
import TopSection from "./TopSection"

type CurrentScreenRouteProp = RouteProp<EntryStackParamList, "Dashboard">
export default () => {
  const { onLogout } = useAuth()
  const route = useRoute<CurrentScreenRouteProp>()
  const params = route.params
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const navigation = useNavigation()
  const [brand, setBrand] = useState()
  const [startDateTime, setStartDateTime] = useState<Date>(null)
  const midSectionRef = useRef(null),
    [sales, setSales] = useState([]),
    [settlement, setSettlement] = useState([]),
    [activityCount, setActivityCount] = useState([]),
    [interior, setInterior] = useState(),
    [invoiceManual, setInvoiceManual] = useState(),
    [totalActivity, setTotalActivity] = useState<number>(),
    [startDate, setStartDate] = useState(
      moment().startOf("month").format("YYYY-MM-DD"),
    ),
    [endDate, setEndDate] = useState(
      moment().endOf("month").format("YYYY-MM-DD"),
    )
  const date = {
    startDate: moment().startOf("month").format("YYYY-MM-DD"),
    endDate: moment().endOf("month").format("YYYY-MM-DD"),
  }
  const newDate =
    startDateTime !== null
      ? moment(startDateTime).startOf("month").format("YYYY-MM-DD")
      : route?.params?.startDate === undefined
      ? startDate
      : moment(params?.startDate).format("YYYY-MM-DD")

  const newEndDate =
    startDateTime !== null
      ? moment(startDateTime).endOf("month").format("YYYY-MM-DD")
      : params?.endDate === undefined
      ? endDate
      : moment(params?.endDate).endOf("month").format("YYYY-MM-DD")
  const {
    queries: [
      { data: userData },
      { data: channelData },
      { data: topSalesData },
    ],
    meta: { isLoading },
  } = useMultipleQueries([
    useUserLoggedInData(),
    useChannelDefault(),
    useSuperstarList("target", "yahaha", newDate, newEndDate),
  ] as const)
  const SalesRevenue = useQuery<string, any>(["revenue", loggedIn], () => {
    return axios
      .get(`targets`, {
        params: {
          "filter[type]": "DEALS_INVOICE_PRICE",
          "filter[company_id]": params?.filter || userData.companyId,
          "filter[start_after]": newDate,
          "filter[end_before]": newEndDate,
          "filter[is_dashboard]": 1,
          "filter[reportable_type]":
            params?.sales !== undefined
              ? "USER"
              : params?.channel !== undefined
              ? "USER"
              : userData.type === "DIRECTOR"
              ? userData?.reportable_type
              : userData.type === "SUPERVISOR"
              ? "USER"
              : userData.type === "SALES"
              ? userData?.reportable_type
              : userData?.companyId,
          "filter[reportable_ids]":
            params?.filter !== undefined
              ? params?.filter
              : params?.sales !== undefined
              ? params?.sales
              : params?.channel !== undefined
              ? params?.channel
              : userData.type === "DIRECTOR"
              ? userData?.companyId
              : userData.type === "SUPERVISOR"
              ? userData?.id
              : userData.type === "SALES"
              ? userData.id
              : userData?.companyId,
        },
      })
      .then((res) => {
        setSales(res.data.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })
  const Settlement = useQuery<string, any>(["settlement", loggedIn], () => {
    return axios
      .get(`targets`, {
        params: {
          "filter[type]": "ORDER_SETTLEMENT_COUNT",
          "filter[company_id]": params?.filter || userData.companyId,
          "filter[start_after]": newDate,
          "filter[end_before]": newEndDate,
          "filter[is_dashboard]": 1,
          "filter[reportable_type]":
            params?.sales !== undefined
              ? "USER"
              : params?.channel !== undefined
              ? "USER"
              : userData.type === "DIRECTOR"
              ? userData?.reportable_type
              : userData.type === "SUPERVISOR"
              ? "USER"
              : userData.type === "SALES"
              ? userData?.reportable_type
              : userData?.companyId,
          "filter[reportable_ids]":
            params?.filter !== undefined
              ? params?.filter
              : params?.sales !== undefined
              ? params?.sales
              : params?.channel !== undefined
              ? params?.channel
              : userData.type === "DIRECTOR"
              ? userData?.companyId
              : userData.type === "SUPERVISOR"
              ? userData?.id
              : userData.type === "SALES"
              ? userData.id
              : userData?.companyId,
        },
      })
      .then((res) => {
        setSettlement(res?.data?.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })
  const ActivityCount = useQuery<string, any>(
    ["activitycount", loggedIn],
    () => {
      return axios
        .get(`targets`, {
          params: {
            "filter[type]": "ACTIVITY_COUNT",
            "filter[company_id]": params?.filter || userData.companyId,
            "filter[start_after]": newDate,
            "filter[end_before]": newEndDate,
            "filter[is_dashboard]": 1,
            "filter[reportable_type]":
              params?.sales !== undefined
                ? "USER"
                : params?.channel !== undefined
                ? "USER"
                : userData.type === "DIRECTOR"
                ? userData?.reportable_type
                : userData.type === "SUPERVISOR"
                ? "USER"
                : userData.type === "SALES"
                ? userData?.reportable_type
                : userData?.companyId,
            "filter[reportable_ids]":
              params?.filter !== undefined
                ? params?.filter
                : params?.sales !== undefined
                ? params?.sales
                : params?.channel !== undefined
                ? params?.channel
                : userData.type === "DIRECTOR"
                ? userData?.companyId
                : userData.type === "SUPERVISOR"
                ? userData?.id
                : userData.type === "SALES"
                ? userData.id
                : userData?.companyId,
          },
        })
        .then((res) => {
          setActivityCount(res?.data?.data)
        })
        .catch((error) => {
          if (error.response) {
            console.log(error.response)
          }
        })
    },
  )
  const brandCategory = useQuery<string, any>(["brand", loggedIn], () => {
    return axios
      .get(`dashboard/brand-categories`, {
        params: {
          company_id: params?.filter || userData.companyId,
          start_at: newDate,
          end_at: newEndDate,
          user_id: params?.sales,
          channel_id: params?.channel,
        },
      })
      .then((res) => {
        setBrand(res.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })
  const interiorDesign = useQuery<string, any>(["interior", loggedIn], () => {
    return axios
      .get(`dashboard/interior-designs`, {
        params: {
          company_id: params?.filter || userData.companyId,
          channel_id: params?.channel || null,
          start_at: newDate,
          end_at: newEndDate,
        },
      })
      .then((res) => {
        setInterior(res?.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })
  const InvoiceCount = useQuery<string, any>(["InvoiceCount", loggedIn], () => {
    return axios
      .get(`dashboard/cart-demands`, {
        params: {
          user_id: params?.sales || null,
          company_id: params?.filter || null,
          channel_id: params?.channel || null,
          start_at: newDate,
          end_at: newEndDate,
        },
      })
      .then((res) => {
        setInvoiceManual(res.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })

  const ActivityList = useQuery<string, any>(["ActivityList", loggedIn], () => {
    return axios
      .get(`activities/report`, {
        params: {
          user_id: params?.sales || null,
          company_id: params?.filter || null,
          channel_id: params?.channel || null,
          start_at: newDate,
          end_at: newEndDate,
        },
      })
      .then((res) => {
        setTotalActivity(res.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })

  const SalesFilter = () => (
    <Div w={102} mr={15}>
      <MonthPickerInput
        placeholder="Filter Month"
        onSelect={setStartDateTime}
        value={startDateTime}
      />
    </Div>
  )
  const loadData = () => {
    if (navigation.isFocused()) {
      SalesRevenue.refetch()
      Settlement.refetch()
      ActivityCount.refetch()
      brandCategory.refetch()
      interiorDesign.refetch()
      InvoiceCount.refetch()
      ActivityList.refetch()
    }
  }
  useEffect(() => {
    loadData()
  }, [navigation.isFocused(), startDateTime, params])
  return (
    <ScrollView
      contentContainerStyle={{ flexGrow: 1 }}
      showsVerticalScrollIndicator={false}
      refreshControl={
        <RefreshControl refreshing={isLoading} onRefresh={() => loadData()} />
      }
    >
      <Div flex={1}>
        <TopSection userData={userData} channelData={channelData} />
        {/* <MidSection userData={userData} ref={midSectionRef} /> */}
        {userData.reportable_type === "COMPANY" ||
        userData.reportable_type === "CHANNEL" ? (
          <>
            <Div h={10} />
            <FilterSection isSales={false} />
            <Div h={10} />
            <SalesSection
              data={sales}
              userData={userData}
              date={date}
              filter={params}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={
                startDateTime !== null
                  ? startDateTime
                  : params?.endDate || endDate
              }
            />
            <InteriorDesign
              data={interior}
              userData={userData}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={params?.endDate || endDate}
              filter={params}
              settlement={settlement}
            />
            <Invoice
              data={invoiceManual}
              userData={userData}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={params?.endDate || endDate}
              filter={params}
            />
            <SettlementCount
              data={settlement}
              userData={userData}
              channelData={channelData}
              filter={params}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={params?.endDate || endDate}
              totalActivity={totalActivity}
            />
            <FollowSection data={activityCount} />
            <BrandCategory
              data={brand}
              userData={userData}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={params?.endDate || endDate}
            />
            <BotSection
              userData={userData}
              data={topSalesData?.data}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={params?.endDate || endDate}
            />
          </>
        ) : (
          <>
            <Div row justifyContent="space-between">
              <FilterSection isSales={true} />
              <SalesFilter />
            </Div>
            <Div h={10} />
            <SalesSection
              data={sales}
              userData={userData}
              date={date}
              startDate={startDateTime !== null ? startDateTime : startDate}
              endDate={startDateTime !== null ? startDateTime : endDate}
              filter={undefined}
            />
            <InteriorDesign
              data={interior}
              userData={userData}
              settlement={settlement}
              startDate={startDateTime !== null ? startDateTime : startDate}
              endDate={startDateTime !== null ? startDateTime : endDate}
              filter={undefined}
            />
            <Invoice
              data={invoiceManual}
              userData={userData}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={params?.endDate || endDate}
              filter={params}
            />
            <SettlementCount
              data={settlement}
              userData={userData}
              channelData={channelData}
              startDate={startDateTime !== null ? startDateTime : startDate}
              endDate={startDateTime !== null ? startDateTime : endDate}
              filter={undefined}
              totalActivity={totalActivity}
            />
            <FollowSection data={activityCount} />
            <BotSection
              userData={userData}
              data={topSalesData?.data}
              startDate={!!params?.startDate ? params.startDate : startDate}
              endDate={params?.endDate || endDate}
            />
          </>
        )}
        <Div px={20} pt={20} bg="#F9F7F7" mb={10}>
          {/* <Button
            w={"100%"}
            mb={10}
            bg="white"
            borderWidth={1}
            borderColor="primary"
            color="black"
            fontSize={14}
            onPress={() => {
              navigation.navigate("UserSelectChannel")
            }}
          >
            Change Channel
          </Button> */}
          <TouchableOpacity
            onPress={() => {
              onLogout()
            }}
          >
            <LinearGradient
              style={{ height: 40, justifyContent: "center", borderRadius: 4 }}
              locations={[0.5, 1.0]}
              colors={["#20B5C0", "#17949D"]}
            >
              <Text color="white" fontSize={14} textAlign="center">
                Logout
              </Text>
            </LinearGradient>
          </TouchableOpacity>
        </Div>
      </Div>
    </ScrollView>
  )
}
