import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, StyleSheet } from "react-native"
import { TouchableOpacity } from "react-native-gesture-handler"
import { Div, Text } from "react-native-magnus"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

const InteriorDesignDetail = () => {
  const [data, setData] = useState([])
  const route = useRoute()
  const navigation = useNavigation()
  const params = route.params
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [loading, setLoading] = useState(false)
  const interiorDesign = useQuery<string, any>(
    ["interiorDesign", loggedIn],
    () => {
      setLoading(true)
      return axios
        .get(
          `dashboard/interior-designs/detail/${params.id}?start_at=${moment(
            params?.date,
          ).format("YYYY-MM-DD")}&end_at=${moment(params?.date)
            .endOf("month")
            .format("YYYY-MM-DD")}`,
        )
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
    // onPress={() =>
    //   navigation.navigate("InteriorActivity", {
    //     params,
    //     id: item.id,
    //   })
    // }
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="#c4c4c4">
        <Div flex={2.5}>
          <Text textAlign="center">
            {moment(item?.date).format("DD MMM YYYY")}
          </Text>
        </Div>
        <Div flex={3}>
          <Text fontWeight="normal" textAlign="center">
            {item?.channel}
          </Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item?.sales}</Text>
        </Div>
        <Div flex={4}>
          <Text textAlign="center">{item?.total}</Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )
  useEffect(() => {
    interiorDesign.refetch()
  }, [])
  if (loading === true) {
    return <Loading />
  }
  return (
    <Div flex={1} bg="white">
      <Text fontWeight="bold" ml={20} mt={20}>
        Interior Design
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
              <Div flex={2.5}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Date
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Channel
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Sales
                </Text>
              </Div>
              <Div flex={4}>
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

export default InteriorDesignDetail

const styles = StyleSheet.create({})
