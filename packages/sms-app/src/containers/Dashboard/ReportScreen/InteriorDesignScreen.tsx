import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, StyleSheet } from "react-native"
import { TouchableOpacity } from "react-native-gesture-handler"
import { Div, ScrollDiv, Text } from "react-native-magnus"
import { useQuery } from "react-query"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

const InteriorDesignScreen = () => {
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
          `dashboard/interior-designs/detail?start_at=${moment(
            params?.startDate,
          ).format("YYYY-MM-DD")}&end_at=${moment(params?.startDate)
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
      onPress={() =>
        navigation.navigate("InteriorDesignDetail", {
          params,
          id: item.interior_design_id,
          date: params?.startDate,
        })
      }
    >
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="#c4c4c4">
        <Div flex={3}>
          <Text color="primary" fontWeight="normal" textAlign="left" ml={20}>
            {item?.interior_design?.name}
          </Text>
        </Div>
        <Div flex={3}>
          <Text color="primary" fontWeight="normal" textAlign="left" ml={20}>
            {item?.total}
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
    <ScrollDiv bg="white" bounces={false} flex={1}>
      <Text fontWeight="bold" ml={20} mt={20}>
        List of interior design ({data.length})
      </Text>
      <Div mt={20}>
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
                <Text color="white" fontWeight="bold" textAlign="left" ml={20}>
                  ID Name
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="left" ml={20}>
                  Total Revenue
                </Text>
              </Div>
            </Div>
          }
        />
      </Div>
    </ScrollDiv>
  )
}

export default InteriorDesignScreen

const styles = StyleSheet.create({})
