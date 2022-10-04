/* eslint-disable react-hooks/exhaustive-deps */
import { useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, Pressable, StyleSheet, View } from "react-native"
import { Button, Div, Icon, ScrollDiv, Text } from "react-native-magnus"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { formatCurrency } from "helper"

const BrandDetail = () => {
  const route = useRoute()
  const axios = useAxios()
  const [currentIndex, setCurrentIndex] = useState(0)
  const { loggedIn } = useAuth()
  const params = route.params
  const [data, setData] = useState([])
  const [loading, setLoading] = useState(false)
  const brandCategory = useQuery<string, any>(["brand", loggedIn], () => {
    setLoading(true)
    return axios
      .get(
        `dashboard/sales-estimation/${params.id}?company_id=${
          params?.userData?.companyId
        }&start_at=${moment(params?.startDate).format(
          "YYYY-MM-DD",
        )}&end_at=${moment(params?.endDate)
          .endOf("month")
          .format("YYYY-MM-DD")}`,
      )
      .then((res) => {
        setData(res?.data)
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
  return (
    <ScrollDiv flex={1} bg="white">
      <Div
        row
        mt={20}
        alignItems="center"
        justifyContent="space-between"
        mx={20}
        mb={20}
      >
        <Text fontWeight="bold" fontSize={16}>
          {params?.userData?.name}
        </Text>
      </Div>
      {Object.keys(data).map((key, i) => (
        <>
          <Pressable
            onPress={() => setCurrentIndex(i === currentIndex ? null : i)}
          >
            <Div
              bg="white"
              p={20}
              borderBottomWidth={5}
              borderBottomColor="#F8F8F8"
            >
              <Div row justifyContent="space-between">
                <Text fontSize={12} color="#17949D" fontWeight="bold">
                  {data[key]?.[0]?.bum}
                </Text>
                <Icon
                  name={i === currentIndex ? "chevron-up" : "chevron-down"}
                  fontFamily="FontAwesome5"
                  fontSize={20}
                  bottom={6}
                />
              </Div>
              <Text fontSize={12} fontWeight="bold">
                {data[key]?.[0]?.sales}
              </Text>
              <Text fontSize={12} fontWeight="normal">
                {data[key]?.[0]?.customer}
              </Text>
            </Div>
          </Pressable>
          {i === currentIndex && (
            <>
              <Div py={14} row bg="#17949D">
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Brand
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Order Value
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Estimation
                  </Text>
                </Div>
              </Div>
              <Div
                py={14}
                row
                bg="white"
                borderBottomWidth={1}
                borderColor="#c4c4c4"
              >
                <Div flex={3}>
                  {data[key]?.map((e, i) => (
                    <Text textAlign="center" my={2}>
                      {i + 1}. {e.brand}
                    </Text>
                  ))}
                </Div>
                <Div flex={3}>
                  {data[key]?.map((e) => (
                    <Text textAlign="center" my={2}>
                      {formatCurrency(e.order_value)}
                    </Text>
                  ))}
                </Div>
                <Div flex={3}>
                  {data[key]?.map((e) => (
                    <Text textAlign="center" my={2}>
                      {formatCurrency(e.estimated_value)}
                    </Text>
                  ))}
                </Div>
              </Div>
            </>
          )}
        </>
      ))}
    </ScrollDiv>
  )
}

export default BrandDetail
