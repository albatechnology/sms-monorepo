/* eslint-disable react-hooks/exhaustive-deps */
import { useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, ScrollView, StyleSheet, View } from "react-native"
import { Button, Div, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

const TopSalesDetail = () => {
  const { loggedIn } = useAuth()
  const route = useRoute()
  const params = route.params
  const axios = useAxios()
  const [topsales, setTopSales] = useState([])
  const [loading, setLoading] = useState(false)
  const [categorySales, setCategorySales] = useState("target")
  const topSales = useQuery<string, any>(["topsalesdetail", loggedIn], () => {
    setLoading(true)
    return axios
      .get(
        `dashboard/top-sales/${categorySales}?limit=50&start_at=${moment(
          params?.startDate,
        ).format("YYYY-MM-DD")}&end_at=${moment(params?.endDate)
          .endOf("month")
          .format("YYYY-MM-DD")}&type=sales&${params.type}_id=${params.id}`,
      )
      .then((res) => {
        setTopSales(res.data)
      })
      .catch((error) => {
        if (error) {
          console.log(error.response)
        }
      })
      .finally(() => {
        setLoading(false)
      })
  })
  useEffect(() => {
    topSales.refetch()
  }, [categorySales])
  if (loading === true) {
    return <Loading />
  }
  const renderTopSales = ({ item }) => (
    <>
      {categorySales === "target" ? (
        <Div
          py={14}
          bg="white"
          row
          borderBottomWidth={1}
          borderBottomColor="#c4c4c4"
        >
          <Div flex={1.5}>
            <Text fontWeight="normal" textAlign="center">
              {item.priority}
            </Text>
          </Div>
          <Div flex={4}>
            <Text fontWeight="normal" textAlign="center">
              {item?.model?.name}
            </Text>
          </Div>
          <Div flex={3}>
            <Text fontWeight="normal" textAlign="center">
              {item.percentage}
            </Text>
          </Div>
          <Div flex={3}>
            <Text fontWeight="normal" textAlign="center">
              {item.value}
            </Text>
          </Div>
        </Div>
      ) : (
        <Div
          py={14}
          bg="white"
          row
          borderBottomWidth={1}
          borderBottomColor="#c4c4c4"
        >
          <Div flex={1}>
            <Text fontWeight="normal" textAlign="center">
              {item?.priority}
            </Text>
          </Div>
          <Div flex={4}>
            <Text fontWeight="normal" textAlign="center">
              {item?.model?.name}
            </Text>
          </Div>
          <Div flex={3}>
            <Text fontWeight="normal" textAlign="center">
              {item?.value}
            </Text>
          </Div>
        </Div>
      )}
    </>
  )
  return (
    <Div bg="white" flex={1}>
      <Div row justifyContent="space-between" p={15}>
        <Text fontWeight="bold" fontSize={16}>
          Top Sales
        </Text>
        <Div row alignSelf="center">
          <Button
            onPress={() => setCategorySales("value")}
            bg="white"
            borderWidth={2}
            mx={5}
            borderColor={categorySales === "value" ? "#17949D" : "grey"}
            color={categorySales === "value" ? "#17949D" : "grey"}
            underlayColor="red100"
          >
            Value
          </Button>
          <Button
            onPress={() => setCategorySales("target")}
            bg="white"
            borderWidth={2}
            borderColor={categorySales === "value" ? "grey" : "#17949D"}
            color={categorySales === "value" ? "grey" : "#17949D"}
            underlayColor="red100"
          >
            Target
          </Button>
        </Div>
      </Div>
      <View style={styles.content}>
        <FlatList
          renderItem={renderTopSales}
          data={topsales}
          ListHeaderComponent={
            categorySales === "target" ? (
              <Div py={14} row bg="#17949D">
                <Div flex={1.5}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    No.
                  </Text>
                </Div>
                <Div flex={4}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    {params?.type} Name
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Meet Goal
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Revenue
                  </Text>
                </Div>
              </Div>
            ) : (
              <Div py={14} row bg="#17949D">
                <Div flex={1}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    No.
                  </Text>
                </Div>
                <Div flex={4}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    {params?.type} Name
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Revenue
                  </Text>
                </Div>
              </Div>
            )
          }
        />
      </View>
    </Div>
  )
}

export default TopSalesDetail

const styles = StyleSheet.create({
  content: {
    height: heightPercentageToDP(70),
    marginTop: heightPercentageToDP(2),
  },
})
