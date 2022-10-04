import { useNavigation, useRoute } from "@react-navigation/native"
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

const ReportBrandsScreen = () => {
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const navigation = useNavigation()
  const route = useRoute()
  const [filter, setFilter] = useState({})
  const [sort, setSort] = useState("Lead")
  const ReportDataBrands = useQuery<string, any>(
    ["ReportDataBrandsDetails", loggedIn],
    () => {
      return axios
        .get(`dashboard/report-brands/details`, {
          params: {
            channel_id: route?.params?.filter?.filterChannelName,
            user_id: route.params.id,
            start_date: moment(route?.params?.filter?.filterUserId)
              .startOf("month")
              .format("YYYY-MM-DD"),
            end_date: moment(route?.params?.filter?.filterCustomerHasActivity)
              .endOf("month")
              .format("YYYY-MM-DD"),
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
    },
  )
  useEffect(() => {
    ReportDataBrands.refetch()
  }, [filter])
  const renderItem = ({ item }) => (
    <TouchableOpacity
      onPress={() => navigation.navigate("ReportCompareScreen", item)}
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="grey">
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.customer}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {formatCurrency(item.total_estimated) || "Rp. 0"}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {formatCurrency(item.total_quotation) || "Rp. 0"}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.phone}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.source}
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
      {/* <ReportBrandFilter
        activeFilterValues={filter}
        activeSort={sort}
        onSetSort={setSort}
        onSetFilter={setFilter}
      /> */}
      {sort === "Lead" ? (
        <>
          <Div p={15}>
            <Div row justifyContent="space-between" p={5}>
              <Text>Sales :</Text>
              <Text>{route.params.sales}</Text>
            </Div>
            <Div row justifyContent="space-between" p={5}>
              <Text>Channel :</Text>
              <Text>{route.params.channel}</Text>
            </Div>
          </Div>
          <FlatList
            bounces={false}
            data={data}
            renderItem={renderItem}
            ListHeaderComponent={
              <Div py={14} row bg="#17949D">
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Leads
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Estimated
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Quotation
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Phone
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Source
                  </Text>
                </Div>
              </Div>
            }
          />
        </>
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

export default ReportBrandsScreen
