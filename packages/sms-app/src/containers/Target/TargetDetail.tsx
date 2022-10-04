import { useRoute } from "@react-navigation/native"
import React, { useCallback, useMemo, useState } from "react"
import { Dimensions, ScrollView } from "react-native"
import { SceneMap, TabBar, TabView } from "react-native-tab-view"

import { responsive } from "helper"

import TargetList from "./TargetList"

const TargetDetail = () => {
  const route = useRoute()
  const userData = route.params
  const [index, setIndex] = React.useState(0)
  console.log(userData)
  const routes = useMemo(() => {
    if (userData?.type === "DIRECTOR") {
      return [
        { key: "bum", title: "Bum" },
        { key: "leader", title: "Leader" },
        { key: "store", title: "Store" },
        { key: "sales", title: "Sales" },
      ]
    }else if(userData?.as === "BUM"){
      return [
      { key: "leader", title: "Leader" },
      { key: "store", title: "Store" },
      { key: "sales", title: "Sales" },
      ]
    }
    return [
      // { key: "leader", title: "Leader" },
      { key: "store", title: "Store" },
      { key: "sales", title: "Sales" },
    ]
  }, [userData])
  const salesScene = useCallback(() => <TargetList type="sales" />, [])
  const storeScene = useCallback(() => <TargetList type="store" />, [])
  const leaderScene = useCallback(() => <TargetList type="store_leader" />, [])
  const bumScene = useCallback(() => <TargetList type="bum" />, [])
  const renderScene = useMemo(
    () =>
      SceneMap({
        sales: salesScene,
        store: storeScene,
        leader: leaderScene,
        bum: bumScene,
      }),
    [salesScene, storeScene, bumScene, leaderScene],
  )

  const renderTabBar = useCallback(
    (props) => (
      <TabBar
        {...props}
        indicatorStyle={{ backgroundColor: "#17949D" }}
        style={{ backgroundColor: "white" }}
        labelStyle={{
          fontSize: responsive(12),
          color: "#17949D",
          fontWeight: "bold",
        }}
      />
    ),
    [],
  )
  return (
    <>
      <TabView
        navigationState={{ index, routes }}
        renderTabBar={renderTabBar}
        renderScene={renderScene}
        onIndexChange={setIndex}
        initialLayout={{ width: Dimensions.get("window").width }}
      />
    </>
  )
}

export default TargetDetail
