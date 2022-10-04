import { useNavigation } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useEffect, useState } from "react"
import {
  FlatList,
  Pressable,
  RefreshControl,
  ScrollView,
  View,
} from "react-native"
import { Div, Icon, Input } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import ChannelFilter, { ChannelFilterType } from "filters/ChannelFilter"

import useChannelList from "api/hooks/channel/useChannelList"

import { ProductStackParamList } from "Router/MainTabParamList"

import { dataFromPaginated } from "helper/pagination"
import theme, { COLOR_PRIMARY } from "helper/theme"

import { Channel } from "types/Channel"

type CurrentScreenNavigationProp = StackNavigationProp<
  ProductStackParamList,
  "StockSelectChannel"
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const [data, setData] = useState([])
  const [filters, setFilter] = useState("")
  const { loggedIn } = useAuth()
  const [loading, setLoading] = useState(false)
  const axios = useAxios()
  const queryData = useQuery<string, any>(["SelectChannel", loggedIn], () => {
    setLoading(true)
    return axios
      .get(`stocks/indexNew?name=${filters}`)
      .then((res) => {
        setData(res.data.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
      .finally(() => {
        setLoading(false)
      })
  })
  useEffect(() => {
    queryData.refetch()
  }, [filters])
  // const {
  //   queries: [{ data: paginatedData }],
  //   meta: {
  //     isError,
  //     isLoading,
  //     isFetching,
  //     refetch,
  //     manualRefetch,
  //     isManualRefetching,
  //     isFetchingNextPage,
  //     hasNextPage,
  //     fetchNextPage,
  //   },
  // } = useMultipleQueries([useChannelList({ ...filters })] as const)
  // const data: Channel[] = dataFromPaginated(paginatedData)
  // if (isError) {
  //   return <Error refreshing={isFetching} onRefresh={refetch} />
  // }
  return (
    <>
      {/* <ChannelFilter activeFilterValues={filters} onSetFilter={setFilter} /> */}
      <View style={[theme.mX10, theme.mT10, theme.mB10]}>
        <Input
          placeholder="Search by name"
          p={12}
          onChangeText={(value: string) => setFilter(value)}
          focusBorderColor="black"
          prefix={<Icon name="search" color="gray900" fontFamily="Feather" />}
        />
      </View>
      {loading === true ? (
        <Loading />
      ) : (
        <ScrollView horizontal showsHorizontalScrollIndicator={false}>
          <FlatList
            // refreshControl={
            //   <RefreshControl
            //     colors={[COLOR_PRIMARY]}
            //     tintColor={COLOR_PRIMARY}
            //     titleColor={COLOR_PRIMARY}
            //     title="Loading..."
            //     refreshing={isManualRefetching}
            //     onRefresh={manualRefetch}
            //   />
            // }
            contentContainerStyle={[{ width: widthPercentageToDP("100%") }]}
            data={data}
            keyExtractor={({ id }) => `channel_${id}`}
            showsVerticalScrollIndicator={false}
            bounces={false}
            ListEmptyComponent={() => (
              <Text fontSize={14} textAlign="center" p={20}>
                Tidak ada Channel
              </Text>
            )}
            onEndReachedThreshold={0.2}
            // onEndReached={() => {
            //   if (hasNextPage) fetchNextPage()
            // }}
            ListHeaderComponent={
              <Div py={14} row style={{ backgroundColor: "#17949D" }}>
                <Div flex={2}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Product Unit
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Channel
                  </Text>
                </Div>
                <Div flex={2}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Total Stock
                  </Text>
                </Div>
              </Div>
            }
            // ListFooterComponent={() =>
            //   !!data &&
            //   data.length > 0 &&
            //   (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
            // }
            renderItem={({ item: channel }) => (
              <Pressable
                onPress={() =>
                  navigation.navigate("Stock", { channelId: channel.id })
                }
              >
                <Div py={20} borderTopColor="grey" borderTopWidth={0.8} row>
                  <Div flex={2}>
                    <Text textAlign="center">{channel.id}</Text>
                  </Div>
                  <Div flex={3}>
                    <Text textAlign="center">{channel.name}</Text>
                  </Div>
                  <Div flex={2}>
                    <Text textAlign="center">
                      {channel.channel_stocks_sum_stock}
                    </Text>
                  </Div>
                </Div>
              </Pressable>
            )}
          />
        </ScrollView>
      )}
    </>
  )
}
