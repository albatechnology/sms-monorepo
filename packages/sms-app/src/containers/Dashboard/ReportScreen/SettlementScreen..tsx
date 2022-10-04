import { useRoute, useNavigation } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, StyleSheet, TouchableOpacity, View } from "react-native"
import { Div, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

const SettlementScreen = () => {
  const route = useRoute()
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const navigation = useNavigation()
  const params = route.params,
    [data, setData] = useState([]),
    [loading, setLoading] = useState(false)
  const Settlement = useQuery<string, any>(["settlement", loggedIn], () => {
    setLoading(true)
    return axios
      .get(`dashboard/pelunasan`, {
        params: {
          company_id:
            params?.filter?.filter === undefined
              ? params.userData.company_id
              : params.filter.filter,
          start_at: moment(params?.startDate).format("YYYY-MM-DD"),
          end_at: moment(params.endDate).endOf("month").format("YYYY-MM-DD"),
        },
      })
      .then((res) => {
        setData(res.data)
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
  if (loading === true) {
    return <Loading />
  }
  const renderItem = ({ item }) => (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("ActivityDetail", {
          id: item.activity_id,
          isDeals: true,
        })
      }
    >
      <Div
        py={14}
        row
        bg="white"
        borderBottomWidth={0.5}
        borderBottomColor="#c4c4c4"
      >
        <Div flex={3}>
          <Text textAlign="center">{item?.date}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item?.invoice}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item?.customer}</Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )
  return (
    <Div flex={1} bg="white">
      <Text fontWeight="bold" ml={20} mt={20}>
        Jumlah invoice lunas : {data?.length}
      </Text>
      <Div mt={10} style={{ height: heightPercentageToDP(80) }}>
        <FlatList
          bounces={false}
          data={data}
          renderItem={renderItem}
          ListEmptyComponent={
            <Text textAlign="center" fontSize={16} mt={10} color="#c4c4c4">
              Empty List
            </Text>
          }
          ListHeaderComponent={
            <Div py={14} row bg="#17949D">
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Date
                </Text>
              </Div>
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
            </Div>
          }
        />
      </Div>
    </Div>
  )
}

export default SettlementScreen

const styles = StyleSheet.create({})
