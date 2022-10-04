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

const InteriorActivity = () => {
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
        .get(`interior-designs/reports/${params.params.id}/leads/${params?.id}`)
        .then((res) => {
          setData(res.data.data)
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
        navigation.navigate("ActivityDetail", { id: item.id, isDeals: false })
      }
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="#c4c4c4">
        <Div flex={2.5}>
          <Text textAlign="center">
            {moment(item.date).format("DD-MMM-YY")}
          </Text>
        </Div>
        <Div flex={3}>
          <Text
            fontWeight="normal"
            color={
              item.status === "HOT"
                ? "red"
                : item.status === "WARM"
                ? "orange"
                : item.status === "COLD"
                ? "blue"
                : "grey"
            }
            textAlign="center"
          >
            {!item.order ? "No Invoice" : item?.order?.invoice_number}
          </Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item.user.name}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">
            {!!item.order ? item.order.status : "Follow Up"}
          </Text>
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
                  Invoice Number
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Sales
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  status
                </Text>
              </Div>
            </Div>
          }
        />
      </Div>
    </Div>
  )
}

export default InteriorActivity

const styles = StyleSheet.create({})
