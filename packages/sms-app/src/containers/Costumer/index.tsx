import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useCallback, useEffect } from "react"
import { useMemo } from "react"
import { Dimensions } from "react-native"
import { SceneMap, TabBar, TabView } from "react-native-tab-view"

import useMultipleQueries from "hooks/useMultipleQueries"

import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { responsive } from "helper"
import { COLOR_PRIMARY } from "helper/theme"

import LeadFab from "./LeadFab"
import LeadList from "./LeadList"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "CustomerList">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const {
    queries: [{ data: userData }],
    meta: { isError, isLoading, isFetching },
  } = useMultipleQueries([useUserLoggedInData()] as const)
  const { data } = useUserLoggedInData()
  const [index, setIndex] = React.useState(0)

  const showUnhandledLeadsTab = useMemo(
    () => data.type !== "SALES",
    [data.type],
  )

  const routes = useMemo(() => {
    if (showUnhandledLeadsTab) {
      return [
        { key: "unhandledLead", title: "Unhandled" },
        { key: "leads", title: "Leads" },
        { key: "prospect", title: "Prospect" },
        { key: "director", title: "Customer" },
      ]
    }

    return [
      { key: "leads", title: "Leads" },
      { key: "prospect", title: "Prospect" },
      { key: "customer", title: "Customer" },
      { key: "drop", title: "Drop" },
    ]
  }, [showUnhandledLeadsTab])

  useEffect(() => {
    // Set Header
    navigation.setOptions({ title: `${routes[index].title} List` })
  }, [index])

  const unhandledLeadScene = useCallback(
    () => <LeadList isUnhandled type="LEADS" />,
    [],
  )
  const leadScene = useCallback(() => <LeadList type="LEADS" />, [])
  const prospectScene = useCallback(() => <LeadList type="PROSPECT" />, [])
  const customerScene = useCallback(() => <LeadList type="DEAL" />, [])
  const dropScene = useCallback(() => <LeadList type="DROP" />, [])
  const directorScene = useCallback(
    () => <LeadList type="DROP" isDirector />,
    [],
  )

  const renderScene = useMemo(
    () =>
      SceneMap({
        unhandledLead: unhandledLeadScene,
        leads: leadScene,
        prospect: prospectScene,
        customer: customerScene,
        drop: dropScene,
        director: directorScene,
      }),
    [
      customerScene,
      leadScene,
      prospectScene,
      unhandledLeadScene,
      dropScene,
      directorScene,
    ],
  )

  const renderTabBar = useCallback(
    (props) => (
      <TabBar
        {...props}
        indicatorStyle={{ backgroundColor: "white" }}
        style={{ backgroundColor: COLOR_PRIMARY }}
        labelStyle={{ fontSize: responsive(10) }}
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
      {userData.type === "DIRECTOR" &&
      userData.app_create_lead === false ? null : (
        <>
          {index === 0 - (!showUnhandledLeadsTab ? 1 : 0) && (
            <LeadFab type="LEADS" isUnhandled />
          )}
          {index === 1 - (!showUnhandledLeadsTab ? 1 : 0) && (
            <LeadFab type="LEADS" />
          )}
          {index === 2 - (!showUnhandledLeadsTab ? 1 : 0) && (
            <LeadFab type="PROSPECT" />
          )}
        </>
      )}
    </>
  )
}
