import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { Pressable, RefreshControl, useWindowDimensions } from "react-native"
import { FlatList } from "react-native-gesture-handler"
import { Div, Fab } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Image from "components/Image"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useQATopicList from "api/hooks/qaTopic/useQATopicList"

import { ChatStackParamList, MainTabParamList } from "Router/MainTabParamList"

import { formatDate } from "helper"
import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

import { QATopic } from "types/QATopic"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ChatStackParamList, "Chat">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const { width: screenWidth } = useWindowDimensions()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const {
    queries: [{ data: qaTopicPaginatedData }],
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
  } = useMultipleQueries([useQATopicList()] as const)

  const data: QATopic[] = dataFromPaginated(qaTopicPaginatedData)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <>
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
        contentContainerStyle={[{ flexGrow: 1 }, s.bgWhite]}
        data={data}
        keyExtractor={({ id }) => `chat${id}`}
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
        renderItem={({ item, index }) => (
          <Pressable
            onPress={() => navigation.navigate("ChatDetail", { id: item.id })}
          >
            <Div
              px={20}
              pt={20}
              pb={10}
              borderBottomWidth={0.8}
              borderBottomColor={COLOR_DISABLED}
              row
            >
              <Image
                source={{
                  uri: item?.images?.length > 0 ? item?.images[0].url : null,
                }}
                width={0.1 * screenWidth}
                scalable
                style={{ borderRadius: 999 }}
              />

              <Div ml={10} flex={1}>
                <Text
                  fontSize={12}
                  fontWeight="bold"
                  numberOfLines={2}
                  ellipsizeMode="tail"
                  mb={5}
                >
                  {item.subject}
                </Text>
                <Text mb={5}>
                  {!!item.latestMessage
                    ? `${item?.latestMessage?.sender?.name}: ${item.latestMessage.content}`
                    : null}
                </Text>
                <Text color="grey" fontSize={10} textAlign="right">
                  {formatDate(item.updatedAt)}
                </Text>
              </Div>
            </Div>
          </Pressable>
        )}
      />
      <Fab
        bg="primary"
        fontSize={12}
        h={50}
        w={50}
        shadow="sm"
        // @ts-ignore
        onPress={() => {
          navigation.navigate("AddChat")
        }}
      />
    </>
  )
}
