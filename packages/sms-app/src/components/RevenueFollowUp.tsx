/* eslint-disable react-hooks/exhaustive-deps */
import { useNavigation } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, Pressable, StyleSheet, View } from "react-native"
import { Div, Text } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

const RevenueFollowUp = ({ id, startDate, endDate }) => {
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const navigation = useNavigation()
  const [data, setData] = useState([])
  const [loading, setLoading] = useState(false)
  const ActivityCount = useQuery<string, any>(
    ["activitycount", loggedIn],
    () => {
      setLoading(true)
      return axios
        .get(`targets`, {
          params: {
            "filter[type]": "ACTIVITY_COUNT",
            "filter[company_id]": "",
            "filter[start_after]": moment(startDate)
              .startOf("month")
              .format("YYYY-MM-DD"),
            "filter[end_before]": moment(endDate)
              .endOf("month")
              .format("YYYY-MM-DD"),
            "filter[isDashboard": 1,
            "filter[reportable_type]": "USER",
            "filter[reportable_ids]": id,
          },
        })
        .then((res) => {
          setData(res?.data?.data)
        })
        .catch((error) => {
          console.log(error)
        })
        .finally(() => {
          setLoading(false)
        })
    },
  )
  var percentage = data.reduce((n, { value }) => n + value.value, 0)
  const renderItem = ({ item, index }) => {
    return (
      <Pressable
        style={{ flexDirection: "row", marginTop: heightPercentageToDP(1) }}
        onPress={() =>
          navigation.navigate("ActivityList", {
            isDeals: null,
            filterStatus: item.enum_value,
            filterTargetId: data[0].id,
            startDate: startDate,
            endDate: endDate,
          })
        }
      >
        <Text style={{ width: widthPercentageToDP(16) }}>
          {item.enum_value}
        </Text>
        <Progress.Bar
          borderRadius={10}
          progress={item.value / percentage}
          color={
            item.enum_value === "HOT"
              ? "#E53935"
              : item.enum_value === "COLD"
              ? "#0553B7"
              : item.enum_value === "WARM"
              ? "#FFD13D"
              : item.enum_value === "CLOSED"
              ? "#c4c4c4"
              : "white"
          }
          borderWidth={0}
          height={17}
          width={widthPercentageToDP("38%")}
        />
        <Text
          ml={8}
          textAlign="right"
          style={{ width: widthPercentageToDP(16) }}
        >
          {item.value}
        </Text>
      </Pressable>
    )
  }
  useEffect(() => {
    ActivityCount.refetch()
  }, [id])
  return (
    <View>
      <Text fontSize={16}>Follow Up</Text>
      {data.map((e) => (
        <>
          <Text fontSize={14} fontWeight="bold">
            {e.value.value}
          </Text>
          <FlatList
            data={e.breakdown}
            renderItem={renderItem}
            keyExtractor={(_, idx: number) => idx.toString()}
          />
        </>
      ))}
      {loading === true ? (
        <Div mt={50}>
          <Loading />
        </Div>
      ) : null}
    </View>
  )
}

export default RevenueFollowUp

const styles = StyleSheet.create({})
