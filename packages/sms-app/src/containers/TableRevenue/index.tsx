import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useState } from "react"
import { FlatList, StyleSheet, TouchableOpacity, View } from "react-native"
import { Div, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { formatCurrency } from "helper"

const TableRevenue = () => {
  const navigation = useNavigation()
  const route = useRoute()
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const params = route.params,
    [arr, setArr] = useState([]),
    [loading, setLoading] = useState(false)
  const TableRevenue = useQuery<string, any>(["revenue", loggedIn], () => {
    setLoading(true)
    return axios
      .get(
        `activities?filter[channel_id]=${
          params.user.channel_id || params?.user?.channelId
        }&filter[company_id]=${
          params.user.company_id || params.user.companyId
        }&filter[has_payment]=true`,
        {
          params: {
            "filter[user_id]": params?.user?.id,
            "filter[follow_up_datetime_after]": moment(
              params?.filter?.startDate,
            )
              .startOf("month")
              .format("YYYY-MM-DD"),
            "filter[follow_up_datetime_before]": moment(params?.filter?.endDate)
              .endOf("month")
              .format("YYYY-MM-DD"),
          },
        },
      )
      .then((res) => {
        setArr(res.data.data)
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
  const renderItem = ({ item }) => (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("ActivityDetail", { id: item.id, isDeals: true })
      }
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="#c4c4c4">
        <Div flex={3}>
          <Text textAlign="center">{item?.order?.invoice_number}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">
            {item?.customer?.first_name} {item?.customer?.last_name}
          </Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">
            {formatCurrency(item?.order?.total_price)}
          </Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )

  if (loading === true) {
    return <Loading />
  }

  return (
    <Div flex={1} bg="white">
      <Text fontWeight="bold" ml={20} mt={20}>
        {params?.user?.name}
      </Text>
      <Div mt={20} style={{ height: heightPercentageToDP(80) }}>
        <FlatList
          bounces={false}
          data={arr}
          renderItem={renderItem}
          ListEmptyComponent={
            <Text fontSize={16} textAlign="center" mt={20} color="#c4c4c4">
              Empty List
            </Text>
          }
          ListHeaderComponent={
            <Div py={14} row bg="#17949D">
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Invoice
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Customer Name
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Revenue
                </Text>
              </Div>
            </Div>
          }
        />
      </Div>
    </Div>
  )
}

export default TableRevenue

const styles = StyleSheet.create({})
