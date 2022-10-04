import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useState } from "react"
import {
  FlatList,
  Pressable,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native"
import { Button, Div, Overlay, Text } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"
import { useQuery } from "react-query"

import Loading from "components/Loading"
import RevenueFollowUp from "components/RevenueFollowUp"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

const RevenueScreen = () => {
  const axios = useAxios()
  const route = useRoute()
  const params = route.params
  const navigation = useNavigation()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const [overlayVisible, setOverlayVisible] = useState(false)
  const [loading, setLoading] = useState(false)
  const SalesRevenue = useQuery<string, any>(["revenueBUM", loggedIn], () => {
    setLoading(true)
    return axios
      .get(
        `targets?filter[type]=DEALS_INVOICE_PRICE&filter[start_after]=${moment(
          params?.startDate,
        ).format("YYYY-MM-DD")}&filter[end_before]=${moment(params?.endDate)
          .endOf("month")
          .format(
            "YYYY-MM-DD",
          )}&filter[reportable_type]=USER&filter[company_id]=${
          params?.filter?.filter === undefined
            ? params?.userData?.companyId
            : params?.filter?.filter
        }&filter[supervisor_type_level]=2&filter[reportable_ids]=`,
      )
      .then((res) => {
        setData(res?.data?.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
      .finally(() => {
        setLoading(false)
      })
  })
  const [index, setIndex] = useState("")
  const renderItem = ({ item, index }) => {
    const percentage = item.value.value / item.target.value
    return (
      <>
        <Pressable
          onPress={() =>
            navigation.navigate("RevenueStoreLeader", {
              id: item.user.id,
              startDate: params.startDate,
              endDate: params.endDate,
              filter: params.filter,
            })
          }
        >
          <Div
            mb={10}
            p={12}
            minH={140}
            bg="white"
            shadow="sm"
            rounded={8}
            mx={19}
            mt={2}
          >
            <Div row>
              <Div w={250}>
                <Text fontSize={14}>{item.report.name}</Text>
                <Text fontWeight="bold" color="#2DCC70">
                  {item.value.format}
                </Text>
                <Text>Target {item.target.format}</Text>
              </Div>
              <Progress.Circle
                style={{
                  position: "absolute",
                  marginLeft: widthPercentageToDP(65),
                }}
                unfilledColor="#F9F9F9"
                borderWidth={0}
                size={60}
                progress={
                  percentage === Infinity || isNaN(percentage) ? 0 : percentage
                }
                animated={false}
                thickness={10}
                showsText={true}
                color={"green"}
              />
            </Div>
            <TouchableOpacity
              style={{
                backgroundColor: "#2DCC70",
                width: widthPercentageToDP(20),
                height: 28,
                alignItems: "center",
                justifyContent: "center",
                borderRadius: 5,
                marginTop: 10,
                alignSelf: "flex-end",
              }}
              onPress={() => {
                setOverlayVisible(true)
                setIndex(index)
              }}
            >
              <Text textAlign="center" color="white">
                Follow Up
              </Text>
            </TouchableOpacity>
          </Div>
        </Pressable>
      </>
    )
  }
  if (loading === true) {
    return <Loading />
  }
  return (
    <Div bg="white" flex={1}>
      <Text fontWeight="bold" ml={20} mt={10} fontSize={16}>
        Revenue
      </Text>
      <FlatList data={data} renderItem={renderItem} />
      <Overlay
        visible={overlayVisible}
        h={250}
        onBackdropPress={() => setOverlayVisible(false)}
      >
        <RevenueFollowUp
          id={data[index]?.user.id}
          startDate={params.startDate}
          endDate={params.endDate}
        />
      </Overlay>
    </Div>
  )
}

export default RevenueScreen

const styles = StyleSheet.create({})
