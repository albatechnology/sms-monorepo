import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, StyleSheet, TouchableOpacity } from "react-native"
import { Div, Text } from "react-native-magnus"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

const InvoiceScreen = () => {
  const [data, setData] = useState([])
  const route = useRoute()
  const navigation = useNavigation()
  const params = route.params
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [loading, setLoading] = useState(false)
  const InvoiceScreen = useQuery<string, any>(
    ["invoiceScreen", loggedIn],
    () => {
      setLoading(true)
      return axios
        .get(`dashboard/cart-demands/detail`, {
          params: {
            company_id:
              params?.filter?.filter !== undefined
                ? params.filter.filter
                : params.userData.companyId,
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
    },
  )
  const renderItem = ({ item }) => (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("ActivityDetail", {
          id: item.order.activity_id,
          isDeals: true,
        })
      }
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="#c4c4c4">
        <Div flex={3}>
          <Text textAlign="center">{item?.order?.invoice_number}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">
            {item?.order?.customer?.first_name}{" "}
            {item?.order?.customer?.last_name}
          </Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item?.order?.total_price_format}</Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )
  useEffect(() => {
    InvoiceScreen.refetch()
  }, [])
  if (loading === true) {
    return <Loading />
  }
  return (
    <Div flex={1} bg="white">
      <Text fontWeight="bold" ml={20} mt={20}>
        Invoice Manual
      </Text>
      <Div h={"80%"} mt={20}>
        <FlatList
          renderItem={renderItem}
          data={data}
          ListEmptyComponent={
            <Text textAlign="center" fontSize={16} mt={10} color="#c4c4c4">
              Empty List
            </Text>
          }
          ListHeaderComponent={
            <Div
              py={14}
              row
              bg="#17949D"
              borderBottomWidth={0}
              borderColor="#c4c4c4"
            >
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Invoice Number
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Customer Name
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Total Price
                </Text>
              </Div>
            </Div>
          }
        />
      </Div>
    </Div>
  )
}

export default InvoiceScreen

const styles = StyleSheet.create({})
