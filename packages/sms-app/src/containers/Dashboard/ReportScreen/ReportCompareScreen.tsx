import { useNavigation, useRoute } from "@react-navigation/native"
import React, { useState } from "react"
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

import { formatCurrency } from "helper"

const ReportCompareScreen = () => {
  const route = useRoute()
  const data = route.params
  const renderItem = ({ item }) => (
    <TouchableOpacity onPress={undefined}>
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
      <Div p={15}>
        <Div row justifyContent="space-between" p={5}>
          <Text>Lead :</Text>
          <Text>{data.customer}</Text>
        </Div>
        <Div row justifyContent="space-between" p={5}>
          <Text>Sales :</Text>
          <Text>{data.sales}</Text>
        </Div>
        <Div row justifyContent="space-between" p={5}>
          <Text>Channel :</Text>
          <Text>{data.channel}</Text>
        </Div>
      </Div>
      <FlatList
        bounces="false"
        data={data.product_brands || []}
        renderItem={renderItem}
        ListHeaderComponent={
          <Div py={14} row bg="#17949D">
            <Div flex={3}>
              <Text color="white" fontWeight="bold" textAlign="center">
                Brand Name
              </Text>
            </Div>
            <Div flex={3}>
              <Text color="white" fontWeight="bold" textAlign="center">
                Estimation
              </Text>
            </Div>
            <Div flex={3}>
              <Text color="white" fontWeight="bold" textAlign="center">
                Quotations
              </Text>
            </Div>
          </Div>
        }
        ListFooterComponent={
          <Div py={14} row bg="#353b48">
            <Div flex={3}>
              <Text color="white" textAlign="center">
                Total
              </Text>
            </Div>
            <Div flex={3}>
              <Text color="white" textAlign="center">
                {formatCurrency(data?.total_estimated)}
              </Text>
            </Div>
            <Div flex={3}>
              <Text color="white" textAlign="center">
                {formatCurrency(data?.total_quotation)}
              </Text>
            </Div>
          </Div>
        }
      />
    </Div>
  )
}

export default ReportCompareScreen
