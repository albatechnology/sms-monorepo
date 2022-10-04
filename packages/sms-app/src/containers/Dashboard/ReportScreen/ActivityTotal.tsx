import { useRoute, useNavigation } from "@react-navigation/native"
import moment from "moment"
import React, { useEffect, useState } from "react"
import { FlatList, StyleSheet, TouchableOpacity, View } from "react-native"
import { Div, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import useActivityTotal from "api/hooks/activity/useActivityTotal"

import { dataFromPaginated } from "helper/pagination"

const SettlementScreen = () => {
  const route = useRoute()
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const navigation = useNavigation()
  const params = route.params
  const {
    queries: [{ data: reportData }],
    meta: { isError, isLoading, isFetching, refetch },
  } = useMultipleQueries([
    useActivityTotal(
      params?.startDate,
      params?.filter?.filter === undefined
        ? params.channelData.company_id
        : params.filter.filter,
    ),
  ] as const)
  if (isLoading) {
    return <Loading />
  }
  const data = reportData?.data?.data
  // const Settlement = useQuery<string, any>(["totalDetail", loggedIn], () => {
  //   setLoading(true)
  //   return axios
  //     .get(`activities/report/detail`, {
  //       params: {
  //         company_id:
  //           params?.filter?.filter === undefined
  //             ? params.userData.company_id
  //             : params.filter.filter,
  //         start_at: moment(params?.startDate).format("YYYY-MM-DD"),
  //         end_at: moment(params.endDate).endOf("month").format("YYYY-MM-DD"),
  //       },
  //     })
  //     .then((res) => {
  //       setData(res.data.data)
  //     })
  //     .catch((error) => {
  //       if (error.response) {
  //         console.log(error.response)
  //       }
  //     })
  //     .finally(() => {
  //       setLoading(false)
  //     })
  // })

  const renderItem = ({ item, index }) => (
    <TouchableOpacity onPress={undefined}>
      <Div
        py={14}
        row
        bg="white"
        borderBottomWidth={0.5}
        borderBottomColor="#c4c4c4"
      >
        <Div flex={2}>
          <Text textAlign="center">{index + 1}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item?.name}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item?.channel}</Text>
        </Div>
        <Div flex={3}>
          <Text textAlign="center">{item?.total_activities}</Text>
        </Div>
      </Div>
    </TouchableOpacity>
  )
  return (
    <Div flex={1} bg="white">
      <Text fontWeight="bold" ml={20} mt={20}>
        Total Activity
      </Text>
      <Div mt={10} style={{ height: heightPercentageToDP(80) }}>
        <FlatList
          bounces={false}
          data={data}
          renderItem={renderItem}
          // onEndReachedThreshold={0.2}
          // onEndReached={() => {
          //   if (hasNextPage) fetchNextPage()
          // }}
          ListEmptyComponent={
            <Text textAlign="center" fontSize={16} mt={10} color="#c4c4c4">
              Empty List
            </Text>
          }
          ListFooterComponent={() => <Div h={heightPercentageToDP(5)} />}
          ListHeaderComponent={
            <Div py={14} row bg="#17949D">
              <Div flex={2}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  No.
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Name
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Channel
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Total Activity
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
