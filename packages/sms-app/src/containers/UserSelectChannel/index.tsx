import { useNavigation } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { FlatList, RefreshControl } from "react-native"
import Spinner from "react-native-loading-spinner-overlay"
import { Button, Div } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import useChannelList from "api/hooks/channel/useChannelList"
import useUserSetChannelMutation from "api/hooks/user/useUserSetChannelMutation"

import { EntryStackParamList } from "Router/EntryStackParamList"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_PRIMARY } from "helper/theme"

import { Channel } from "types/Channel"

type CurrentScreenNavigationProp = StackNavigationProp<
  EntryStackParamList,
  "UserSelectChannel"
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const { onLogout } = useAuth()

  const {
    queries: [{ data: paginatedData }],
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
  } = useMultipleQueries([useChannelList()] as const)

  const [setChannel, { isLoading: mutationIsLoading }] =
    useUserSetChannelMutation()

  const data: Channel[] = dataFromPaginated(paginatedData)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <Div p={20} flex={1}>
      <Spinner
        visible={mutationIsLoading}
        textContent={"Loading..."}
        textStyle={{
          color: "#FFF",
        }}
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
        contentContainerStyle={[{ flexGrow: 1 }]}
        data={data}
        keyExtractor={({ id }) => `channel_${id}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListEmptyComponent={() => (
          <Text fontSize={14} textAlign="center" p={20}>
            Tidak ada channel
          </Text>
        )}
        onEndReachedThreshold={0.2}
        onEndReached={() => {
          if (hasNextPage) fetchNextPage()
        }}
        ListFooterComponent={() => (
          <>
            {!!data &&
              data.length > 0 &&
              (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)}
            <Button
              w={"100%"}
              mb={10}
              bg="black"
              color="white"
              shadow="md"
              fontSize={14}
              onPress={() => {
                onLogout()
              }}
            >
              Logout
            </Button>
          </>
        )}
        renderItem={({ item: channel }) => (
          <Button
            borderWidth={1}
            borderColor="gray500"
            p={10}
            my={3}
            w="100%"
            bg="gray100"
            underlayColor="gray300"
            onPress={() => {
              setChannel({ channelId: channel.id }, (x) =>
                x.then(() => {
                  navigation.reset({
                    index: 0,
                    routes: [{ name: "Main" }],
                  })
                }),
              )
            }}
          >
            <Text textAlign="center" fontSize={18}>
              {channel.name}
            </Text>
          </Button>
        )}
      />
    </Div>
  )
}
