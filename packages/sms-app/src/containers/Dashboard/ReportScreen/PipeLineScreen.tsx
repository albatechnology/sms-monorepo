import { useRoute } from "@react-navigation/native"
import React, { FC, useEffect, useState } from "react"
import { StyleSheet, Text, View } from "react-native"
import { Div } from "react-native-magnus"

import Loading from "components/Loading"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import PipelineFilter from "filters/PipelineFilter"

import usePipeline from "api/hooks/pipeline/usePipeline"

import AllScreen from "./PipiLine/AllScreen"
import AllScreenMap from "./PipiLine/AllScreenMap"
import BumScreen from "./PipiLine/BumScreen"
import ChannelScreen from "./PipiLine/ChannelScreen"
import SalesScreen from "./PipiLine/SalesScreen"

type filterTypes = {
  filterUserId: Date
  filterCustomerHasActivity: Date
  filterStatus: string
  filterChannelName: number
}

const PipeLineScreen: FC = () => {
  // const [selected, setSelected] = useState(1)
  const route = useRoute()
  const [filter, setFilter] = useState<filterTypes>({})
  const [sort, setSort] = useState("All")

  const {
    queries: [{ data: reportData }],
    meta: { isLoading, isError, refetch },
  } = useMultipleQueries([
    usePipeline(
      filter?.filterUserId,
      filter?.filterCustomerHasActivity,
      filter?.filterStatus,
      filter?.filterChannelName,
    ),
  ] as const)
  if (isLoading) {
    return <Loading />
  }
  return (
    <Div flex={1}>
      <PipelineFilter
        activeFilterValues={filter}
        activeSort={sort}
        onSetSort={setSort}
        onSetFilter={setFilter}
      />
      <View style={styles.container}>
        {sort === "BUM" ? (
          <View>
            <BumScreen
              filter={filter}
              reportData={reportData?.data?.original?.data}
            />
          </View>
        ) : sort === "Channel" ? (
          <View>
            <ChannelScreen
              filter={filter}
              reportData={reportData?.data?.original?.data}
            />
          </View>
        ) : sort === "Sales" ? (
          <View>
            <SalesScreen
              filter={filter}
              reportData={reportData?.data?.original?.data}
            />
          </View>
        ) : (
          <View>
            <AllScreenMap
              userData={route?.params}
              filter={filter}
              reportData={reportData?.data?.original?.data}
            />
          </View>
        )}
      </View>
    </Div>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    alignItems: "flex-start",
    //   justifyContent: 'center',
  },
})

export default PipeLineScreen
