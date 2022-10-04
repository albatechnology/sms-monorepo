import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useState } from "react"
import { FlatList } from "react-native"
import { Fab } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import ActivityFilter, { ActivityFilterType } from "filters/ActivityFilter"

import useActivityListByCustomer from "api/hooks/activity/useActivityListByCustomer"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  MainTabParamList,
  CustomerStackParamList,
} from "Router/MainTabParamList"

import { dataFromPaginated } from "helper/pagination"
import s from "helper/theme"

import { Activity } from "types/Activity"
import { timeIntervalConfig } from "types/TimeInterval"

import ActivityCard from "./ActivityCard"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "CustomerDetail">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

type PropTypes = {
  customerId: number
  leadId: number
  isDeals?: boolean
}

export default ({ customerId, leadId, isDeals = false }: PropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [filters, setFilter] = useState<ActivityFilterType>({})

  const {
    queries: [{ data: activityPaginatedData }],
    meta: {
      isError,
      isLoading,
      isFetchingNextPage,
      hasNextPage,
      fetchNextPage,
      isFetching,
      refetch,
    },
  } = useMultipleQueries([
    useActivityListByCustomer(customerId, {
      ...filters,
      filterHasPayment: isDeals === true ? isDeals : undefined,
      filterFollowUpDatetimeAfter: filters.filterFollowUpDatetimeAfter
        ? timeIntervalConfig[filters.filterFollowUpDatetimeAfter].value
        : "",
    }),
  ] as const)

  const data: Activity[] = dataFromPaginated(activityPaginatedData)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <>
      <ActivityFilter activeFilterValues={filters} onSetFilter={setFilter} />
      <FlatList
        contentContainerStyle={[{ flexGrow: 1 }, s.bgWhite]}
        data={data}
        keyExtractor={({ id }) => `activity_${id}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        onEndReachedThreshold={0.2}
        onEndReached={() => {
          if (hasNextPage) fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        ListEmptyComponent={() => (
          <Text fontSize={14} textAlign="center" p={20}>
            Kosong
          </Text>
        )}
        renderItem={({ item, index }) => (
          <ActivityCard isDeals={isDeals} activityData={item} />
        )}
      />
      <Fab
        bg="primary"
        fontSize={12}
        h={50}
        w={50}
        shadow="sm"
        // @ts-ignore
        onPress={() =>
          navigation.navigate("AddActivity", { customerId, leadId })
        }
      />
    </>
  )
}
