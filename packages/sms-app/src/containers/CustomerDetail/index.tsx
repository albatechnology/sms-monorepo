import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import Case from "case"
import React, { useCallback, useEffect, useMemo, useState } from "react"
import { Dimensions } from "react-native"
import { SceneMap, TabBar, TabView } from "react-native-tab-view"

import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import { customErrorHandler } from "api/errors"
import useLeadById from "api/hooks/lead/useLeadById"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"
import { COLOR_PRIMARY } from "helper/theme"

import Activity from "./Activity"
import Detail from "./Detail"
import TopSection from "./TopSection"

type CurrentScreenRouteProp = RouteProp<
  CustomerStackParamList,
  "CustomerDetail"
>
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "CustomerDetail">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const leadId = route?.params?.leadId ?? -1
  if (leadId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: leadData }],
    meta,
  } = useMultipleQueries([
    useLeadById(
      leadId,
      customErrorHandler({
        404: () => {
          toast("Lead tidak ditemukan")
          if (navigation.canGoBack()) {
            navigation.goBack()
          } else {
            navigation.navigate("Dashboard")
          }
        },
      }),
    ),
  ] as const)

  const { isError, isLoading, isFetching, refetch } = meta

  useEffect(() => {
    if (!!leadData) {
      navigation.setOptions({
        title: `${Case.title(
          leadData.type !== "DROP" ? leadData.type : "Customer",
        )} Detail`,
      })
    }
  }, [navigation, leadData])

  const detailScene = useCallback(() => <Detail lead={leadData} />, [leadData])
  const activityScreen = useCallback(
    () => <Activity customerId={leadData.customer.id} leadId={leadId} />,
    [leadData, leadId],
  )

  const dealScreen = useCallback(
    () => (
      <Activity customerId={leadData.customer.id} leadId={leadId} isDeals />
    ),
    [leadData, leadId],
  )

  // TabView config
  const [index, setIndex] = useState(0)

  const routes = [
    { key: "detail", title: "Detail" },
    { key: "activity", title: "Activity" },
    { key: "deals", title: "Deals" },
  ]

  const renderScene = useMemo(
    () =>
      SceneMap({
        detail: detailScene,
        activity: activityScreen,
        deals: dealScreen,
      }),
    [detailScene, activityScreen, dealScreen],
  )

  const renderTabBar = (props) => (
    <>
      <TopSection customer={leadData.customer} />
      <TabBar
        {...props}
        indicatorStyle={{ backgroundColor: "white" }}
        style={{ backgroundColor: COLOR_PRIMARY }}
      />
    </>
  )
  //  End of TabView config

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <TabView
      navigationState={{ index, routes }}
      renderTabBar={renderTabBar}
      renderScene={renderScene}
      onIndexChange={setIndex}
      initialLayout={{ width: Dimensions.get("window").width }}
    />
  )
}
