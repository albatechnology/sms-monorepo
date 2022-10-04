import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import Case from "case"
import React, { useState } from "react"
import { FlatList, Pressable, RefreshControl } from "react-native"
import { Div, Icon } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import SalesActivityFilter, {
  ActivityFilterType,
} from "filters/SalesActivityFilter"

import useActivityList from "api/hooks/activity/useActivityList"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import {
  DashboardStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import {
  formatDateOnly,
  formatTimeOnly,
  hexColorFromString,
  responsive,
} from "helper"
import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_PRIMARY } from "helper/theme"

import { Activity } from "types/Activity"
import { getFullName } from "types/Customer"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<DashboardStackParamList, "SalesActivity">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [filters, setFilter] = useState<ActivityFilterType>({})

  const {
    queries: [{ data: userData }, { data: activityPaginatedData }],
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      manualRefetch,
      isManualRefetching,
      isFetchingNextPage,
      hasNextPage,
      fetchNextPage,
    },
  } = useMultipleQueries([
    useUserLoggedInData(),
    useActivityList({ ...filters }, 20),
  ] as const)

  const data: Activity[] = dataFromPaginated(activityPaginatedData)
  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  const renderItem = (item) => {
    let body

    const id = item?.id
    const user = Case.title(item?.user?.name)

    if (item.followUpMethod === "WALK_IN_CUSTOMER") {
      body = (
        <Text>
          <Text fontWeight="bold">{user}</Text> followed up{" "}
          <Text fontWeight="bold">{getFullName(item?.customer)}</Text> in store.
        </Text>
      )
    } else if (item.followUpMethod === "NEW_ORDER") {
      body = (
        <Text>
          <Text fontWeight="bold">{user}</Text> made a new order for{" "}
          <Text fontWeight="bold">{getFullName(item?.customer)}</Text>.
        </Text>
      )
    } else if (item.followUpMethod === "OTHERS") {
      body = (
        <Text>
          <Text fontWeight="bold">{user}</Text> followed up{" "}
          <Text fontWeight="bold">{getFullName(item?.customer)}</Text>.
        </Text>
      )
    } else {
      body = (
        <Text>
          <Text fontWeight="bold">{item?.user?.name}</Text> followed up{" "}
          <Text fontWeight="bold">{getFullName(item?.customer)}</Text> by{" "}
          <Text fontWeight="bold">{Case.title(item?.followUpMethod)}</Text>.
        </Text>
      )
    }

    return (
      <Pressable onPress={() => navigation.navigate("ActivityDetail", { id })}>
        <Div alignItems="flex-start" justifyContent="space-between" mb={20}>
          <Div maxW="70%" row mr={10}>
            <Icon
              name="circle"
              color={`#${hexColorFromString(item?.user?.name)}`}
              fontSize={responsive(10)}
              fontFamily="FontAwesome"
              mr={10}
            />
            <Div>
              <Div maxW="70%" row>
                <Text fontSize={10} color="disabled">
                  {formatDateOnly(item?.followUpDatetime)}
                </Text>
                <Text fontSize={10} ml={10} color="disabled">
                  {formatTimeOnly(item?.followUpDatetime)}
                </Text>
              </Div>
              {body}
            </Div>
          </Div>
        </Div>
      </Pressable>
    )
  }

  return (
    <>
      <SalesActivityFilter
        activeFilterValues={filters}
        onSetFilter={setFilter}
        userData={userData}
      />
      <FlatList
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
        ListHeaderComponent={() => (
          <Text fontWeight="bold" fontSize={12} mb={5}>
            Recent Activity
          </Text>
        )}
        contentContainerStyle={[{ flexGrow: 1 }, s.p20, s.bgWhite]}
        data={data}
        keyExtractor={({ id }) => `salesActivity${id}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListEmptyComponent={() => (
          <Text fontSize={14} textAlign="center" p={20}>
            Kosong
          </Text>
        )}
        onEndReachedThreshold={0.2}
        onEndReached={() => {
          if (hasNextPage) fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        renderItem={({ item, index }) => renderItem(item)}
      />
    </>
  )
}
