import React, { useCallback, useMemo, useState } from "react"
import { Dimensions, RefreshControl } from "react-native"
import { FlatList } from "react-native-gesture-handler"
import { Button, Input } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"
import { SceneMap, TabBar, TabView } from "react-native-tab-view"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import DiscountApprovalFilter, {
  DiscountApprovalFilterType,
} from "filters/DiscountApprovalFilter"

import useOrderByWaitingApproval from "api/hooks/order/useOrderByWaitingApproval"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { responsive } from "helper"
import { dataFromPaginated } from "helper/pagination"
import { COLOR_PRIMARY } from "helper/theme"

import DiscountApprovalCard from "./DiscountApprovalCard"

export default () => {
  const [index, setIndex] = React.useState(0)
  const routes = useMemo(() => {
    return [
      { key: "prospect", title: "Need My Approval" },
      { key: "approval", title: "Approved" },
      { key: "customer", title: "Rejected" },
      { key: "leads", title: "All" },
    ]
  }, [])
  const leadScene = useCallback(() => <DiscountList type={""} />, [])
  const prospectScene = useCallback(
    () => <DiscountList type="WAITING_APPROVAL" />,
    [],
  )
  const customerScene = useCallback(() => <DiscountList type="REJECTED" />, [])
  const approvalScene = useCallback(() => <DiscountList type="APPROVED" />, [])
  const renderScene = useMemo(
    () =>
      SceneMap({
        leads: leadScene,
        prospect: prospectScene,
        customer: customerScene,
        approval: approvalScene,
      }),
    [customerScene, leadScene, prospectScene, approvalScene],
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
      {/* <DiscountApprovalFilter
        activeFilterValues={filters}
        onSetFilter={setFilter}
      /> */}
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
const DiscountList = ({
  type,
  sendToMe,
}: {
  type: string
  sendToMe?: boolean
}) => {
  const [key, setKey] = useState("")
  const {
    queries: [{ data: paginatedData }, { data: userData }],
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      isManualRefetching,
      manualRefetch,
      isFetchingNextPage,
      hasNextPage,
      fetchNextPage,
    },
  } = useMultipleQueries([
    useOrderByWaitingApproval({
      filterApprovalStatus: type,
      filterSearch: key,
      filterApprovalSendToMe: sendToMe,
    }),
    useUserLoggedInData(),
  ] as const)
  const data = dataFromPaginated(paginatedData)
  const [idOfModalShown, setIdOfModalShown] = useState(null)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  // if (isLoading) {
  //   return <Loading />
  // }
  return (
    <>
      <Input
        mx={widthPercentageToDP(5)}
        my={10}
        onChangeText={(val) => setKey(val)}
        value={key}
        placeholder="Search by invoice number or customer name"
      />

      {!!isLoading ? (
        <Loading />
      ) : (
        <FlatList
          contentContainerStyle={{ flexGrow: 1, backgroundColor: "white" }}
          data={data}
          keyExtractor={(item, index) => `discount_approval_${index}`}
          showsVerticalScrollIndicator={false}
          bounces={false}
          ListEmptyComponent={() => (
            <Text fontSize={14} textAlign="center" p={20}>
              Kosong
            </Text>
          )}
          refreshControl={
            <RefreshControl
              colors={[COLOR_PRIMARY]}
              tintColor={COLOR_PRIMARY}
              titleColor={COLOR_PRIMARY}
              title="Loading..."
              refreshing={isManualRefetching}
              onRefresh={manualRefetch}
            />
          }
          onEndReachedThreshold={0.2}
          onEndReached={() => {
            if (hasNextPage) fetchNextPage()
          }}
          ListFooterComponent={() =>
            !!data &&
            data.length > 0 &&
            (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
          }
          renderItem={({ item, index }) => (
            <DiscountApprovalCard
              order={item}
              userData={userData}
              onPress={() => setIdOfModalShown(item.id)}
              modalShown={item.id === idOfModalShown}
              onHideModal={() => setIdOfModalShown(null)}
            />
          )}
        />
      )}
    </>
  )
}
