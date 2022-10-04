import { useNavigation, useRoute } from "@react-navigation/native"
import axios from "axios"
import moment from "moment"
import React, { useState } from "react"
import { FlatList, TouchableOpacity } from "react-native"
import { ScrollView } from "react-native-gesture-handler"
import { Div, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import defaultErrorHandler from "api/errors"

import { formatCurrency } from "helper"

const TotalStatusScreen = () => {
  const axios = useAxios()
  const navigation = useNavigation()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const route = useRoute()
  const params = route.params
  const isLoading = false
  console.warn(params.type, "type")
  const ReportBrands = useQuery<string, any>(["TotalLeads", loggedIn], () => {
    return axios
      .get(`dashboard/report-leads/status`, {
        params: {
          start_date: moment(params?.filter?.filterUserId).format("YYYY-MM-DD"),
          end_date: moment(params?.filter?.filterCustomerHasActivity).format(
            "YYYY-MM-DD",
          ),
          status: params.status,
          supervisor_id: params.type === "SALES" ? "" : params?.id,
          channel_id: params?.channel_id,
          sales_id: params.type === "SALES" ? params?.id : "",
        },
      })
      .then((res) => {
        setData(res.data.data)
      })
      .catch(defaultErrorHandler)
  })
  const renderItem = ({ item }) => (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("ActivityDetail", { id: item.id, isDeals: true })
      }
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="grey">
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.customer.first_name} {item.customer.last_name}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.user.name}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {moment(item.updated_at).format("DD MMM YYYY")}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.follow_up_method}
          </Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )

  if (isLoading) {
    return <Loading />
  }
  return (
    <ScrollView style={{ flex: 1, backgroundColor: "#fff" }}>
      <FlatList
        data={data}
        renderItem={renderItem}
        ListHeaderComponent={
          <Div py={14} row bg="#17949D">
            <Div flex={3}>
              <Text color="white" fontWeight="bold" textAlign="center">
                Lead
              </Text>
            </Div>
            <Div flex={3}>
              <Text color="white" fontWeight="bold" textAlign="center">
                Sales
              </Text>
            </Div>
            <Div flex={3}>
              <Text color="white" fontWeight="bold" textAlign="center">
                Last update
              </Text>
            </Div>
            <Div flex={3}>
              <Text color="white" fontWeight="bold" textAlign="center">
                Follow up
              </Text>
            </Div>
          </Div>
        }
      />
    </ScrollView>
  )
}

export default TotalStatusScreen
