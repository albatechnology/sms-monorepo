import { RouteProp, useRoute } from "@react-navigation/native"
import React, { useState } from "react"
import { FlatList } from "react-native"
import { Div, Text } from "react-native-magnus"

import ActivityCard from "containers/CustomerDetail/Activity/ActivityCard"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import ActivityFilter, { ActivityFilterType } from "filters/ActivityFilter"

import useActivityList from "api/hooks/activity/useActivityList"

import { EntryStackParamList } from "Router/EntryStackParamList"

import { dataFromPaginated } from "helper/pagination"
import s from "helper/theme"

import { Activity } from "types/Activity"

type CurrentScreenRouteProp = RouteProp<EntryStackParamList, "ActivityList">

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()

  const [filters, setFilter] = useState<ActivityFilterType>({})

  const isDeals = route?.params?.isDeals ?? null
  const filterStatus = route?.params?.filterStatus ?? null
  const filterTargetId = route?.params?.filterTargetId ?? null
  const startDate = route?.params?.startDate ?? null
  const endDate = route?.params?.endDate ?? null

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
    useActivityList({
      ...filters,
      ...(isDeals ? { filterHasPayment: isDeals } : {}),
      ...(filterStatus ? { filterStatus } : {}),
      ...(filterTargetId ? { filterTargetId } : {}),
      // ...(startDate ? {filterFollowUpDatetimeAfter : startDate} : {}),
      // ...(endDate ? {filterFollowUpDatetimeBefore : endDate} : {}),
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
    </>
  )
}
