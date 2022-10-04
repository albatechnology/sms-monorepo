import { useNavigation } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { Dimensions, FlatList, Pressable, TouchableOpacity } from "react-native"
import { ScrollView } from "react-native-gesture-handler"
import { Button, Div, Icon, ScrollDiv, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import ReportBrandFilter from "filters/ReportBrandFilter"

import { formatCurrency } from "helper"

const ReportCardScreen = () => {
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const navigation = useNavigation()
  const [filter, setFilter] = useState({})
  const [sort, setSort] = useState("Lead")
  const ReportDataBrands = useQuery<string, any>(
    ["ReportDataBrands", loggedIn],
    () => {
      return axios
        .get(`dashboard/report-brands`, {
          params: {
            channel_id: filter?.filterChannelName,
            start_date: moment(filter?.filterUserId)
              .startOf("month")
              .format("YYYY-MM-DD"),
            end_date: moment(filter?.filterCustomerHasActivity)
              .endOf("month")
              .format("YYYY-MM-DD"),
          },
        })
        .then((res) => {
          setData(res.data.data)
        })
        .catch((error) => {
          if (error.response) {
            console.log(error.response)
          }
        })
    },
  )
  useEffect(() => {
    ReportDataBrands.refetch()
  }, [filter])
  const renderItem = ({ item }) => (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("ReportBrandsScreen", {
          id: item.user_id,
          filter,
          sales: item.sales,
          channel: item.channel,
        })
      }
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="grey">
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.sales}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.total_leads}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.channel}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.bum}
          </Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )
  const renderBrand = ({ item }) => (
    <TouchableOpacity
      onPress={() => navigation.navigate("ReportCompareScreen", item)}
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="grey">
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.product_brand}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {formatCurrency(item.estimated_value)}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {formatCurrency(item.order_value)}
          </Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )
  return (
    <Div flex={1} bg="white">
      <ReportBrandFilter
        activeFilterValues={filter}
        activeSort={sort}
        onSetSort={setSort}
        onSetFilter={setFilter}
      />
      {sort === "Lead" ? (
        <FlatList
          data={data}
          renderItem={renderItem}
          ListHeaderComponent={
            <Div py={14} row bg="#17949D">
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Sales
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Total leads
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Store
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  BUM
                </Text>
              </Div>
            </Div>
          }
        />
      ) : (
        <FlatList
          data={data[0]?.product_brands}
          renderItem={renderBrand}
          ListHeaderComponent={
            <Div py={14} row bg="#17949D">
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Product brand
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Estimated
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Order Value
                </Text>
              </Div>
            </Div>
          }
        />
      )}
    </Div>
  )
}

export default ReportCardScreen
