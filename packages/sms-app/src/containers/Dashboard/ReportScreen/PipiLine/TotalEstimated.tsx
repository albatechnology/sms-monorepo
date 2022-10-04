import { useNavigation, useRoute } from "@react-navigation/native"
import axios from "axios"
import moment from "moment"
import React, { useState } from "react"
import { FlatList, Pressable } from "react-native"
import { ScrollView } from "react-native-gesture-handler"
import { Div, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import defaultErrorHandler from "api/errors"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { formatCurrency } from "helper"

const TotalEstimated = () => {
  const {
    queries: [{ data: userData }],
    meta,
  } = useMultipleQueries([useUserLoggedInData()] as const)
  const navigation = useNavigation()
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const [isLoading, setLoading] = useState(false)
  const route = useRoute()
  const params = route.params
  console.warn(userData.type, "")
  const ReportBrands = useQuery<string, any>(
    ["TotalHotLeads", loggedIn],
    () => {
      setLoading(true)
      return axios
        .get(`dashboard/sales-estimation/all`, {
          params: {
            start_date: moment(params?.startDate).format("YYYY-MM-DD"),
            end_date: moment(params?.endDate).format("YYYY-MM-DD"),
            supervisor_id: params.type === "SALES" ? "" : params?.id,
            channel_id: params?.channel_id,
            sales_id: params.type === "SALES" ? params?.id : "",
          },
        })
        .then((res) => {
          setData(res.data)
        })
        .catch(defaultErrorHandler)
        .finally(() => {
          setLoading(false)
        })
    },
  )

  if (isLoading) {
    return <Loading />
  }
  return (
    <ScrollView style={{ flex: 1, backgroundColor: "#fff" }}>
      <Div>
        <>
          <Div
            py={18}
            row
            bg="#20B5C0"
            justifyContent="center"
            style={{
              height: heightPercentageToDP(9),
              width: widthPercentageToDP(100),
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
                Date
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
                Sales
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
                Customer
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
                Total Estimated
              </Text>
            </Div>
          </Div>
          {Object.keys(data).map((keys, i) => {
            return (
              <Pressable
                onPress={() =>
                  navigation.navigate("TotalEstimatedDetail", data[keys])
                }
              >
                <Div
                  py={14}
                  bg="#fff"
                  row
                  borderBottomWidth={1}
                  borderColor="#c4c4c4"
                  rounded={0}
                  h={heightPercentageToDP(9)}
                  w={widthPercentageToDP(100)}
                  justifyContent="center"
                >
                  <Div flex={3} justifyContent="center">
                    <Text
                      fontWeight="normal"
                      fontSize={8}
                      textAlign="center"
                      allowFontScaling={false}
                    >
                      {moment(data[keys]?.[0].created_at).format("YYYY-MM-DD")}
                    </Text>
                  </Div>
                  <Div flex={3} justifyContent="center">
                    <Text
                      fontWeight="normal"
                      fontSize={8}
                      textAlign="center"
                      allowFontScaling={false}
                    >
                      {data[keys]?.[0]?.sales}
                    </Text>
                  </Div>
                  <Div flex={3} justifyContent="center">
                    <Text
                      fontWeight="normal"
                      fontSize={8}
                      textAlign="center"
                      allowFontScaling={false}
                    >
                      {data[keys]?.[0].customer}
                    </Text>
                  </Div>
                  <Div flex={3} justifyContent="center">
                    <Text
                      fontWeight="normal"
                      fontSize={8}
                      textAlign="center"
                      allowFontScaling={false}
                    >
                      {!!formatCurrency(data[keys]?.[0]?.total_estimated_value)
                        ? formatCurrency(data[keys]?.[0]?.total_estimated_value)
                        : "0"}
                    </Text>
                  </Div>
                </Div>
              </Pressable>
            )
          })}
        </>
      </Div>
    </ScrollView>
  )
}

export default TotalEstimated
