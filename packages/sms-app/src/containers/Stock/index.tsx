import { RouteProp, useNavigation, useRoute } from "@react-navigation/native"
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

import useStockList from "api/hooks/stock/useStockList.ts"

import { ProductStackParamList } from "Router/MainTabParamList"

import Languages from "helper/languages"
import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_PRIMARY } from "helper/theme"
import theme from "helper/theme"

import { Stock } from "types/Stock"

type CurrentScreenNavigationProp = StackNavigationProp<
  ProductStackParamList,
  "Stock"
>

export default () => {
  const route = useRoute()
  const params = route.params
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const [filters, setFilter] = useState("")
  const { loggedIn } = useAuth()
  const axios = useAxios()
  const [data, setData] = useState([])
  const [value, setValue] = React.useState("")
  const [loading, setLoading] = useState(false)
  const queryData = useQuery<string, any>(["Stock", loggedIn], () => {
    setLoading(true)
    return axios
      .get(`stocks/extendedNew/${params.channelId}?name=${filters}`)
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
  // const channelId = route?.params?.channelId ?? -1
  // if (channelId === -1) {
  //   if (navigation.canGoBack()) {
  //     navigation.goBack()
  //   } else {
  //     navigation.navigate("StockSelectChannel"
  //   }
  //   toast(Languages.PageNotFound)
  //   return null
  // }
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
  // } = useMultipleQueries([
  //   useStockList({ ...filters,filterChannelId: channelId.toString(), sort: "id" }),
  // ] as const)
  // const data: Stock[] = dataFromPaginated(paginatedData)

  // if (isError) {
  //   return <Error refreshing={isFetching} onRefresh={refetch} />
  // }

  if (loading) {
    return <Loading />
  }
  return (
    <>
      {/* Filter activeFilterValues={isFetching} onSetFilter={setFilter} /> */}
      <View style={[theme.mX10, theme.mT10]}>
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
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          bounces={false}
        >
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
            contentContainerStyle={[
              s.mT20,
              { width: widthPercentageToDP("150%") },
            ]}
            data={data}
            keyExtractor={(_, idx: number) => idx.toString()}
            showsVerticalScrollIndicator={false}
            bounces={false}
            ListEmptyComponent={() => (
              <Text fontSize={14} textAlign="center" p={20}>
                Stock kosong
              </Text>
            )}
            // onEndReachedThreshold={0.2}
            // onEndReached={() => {
            //   if (hasNextPage) fetchNextPage()
            // }}
            ListHeaderComponent={
              <Div py={14} row style={{ backgroundColor: "#17949D" }}>
                <Div flex={5}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Product Name
                  </Text>
                </Div>
                <Div flex={4}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Brand
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Ready Stock
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Indent
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Outstanding Order
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Outstanding Shipment
                  </Text>
                </Div>
                <Div flex={3}>
                  <Text color="white" fontWeight="bold" textAlign="center">
                    Ready Stock
                  </Text>
                </Div>
              </Div>
            }
            // ListFooterComponent={() =>
            //   !!data &&
            //   data.length > 0 &&
            //   (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
            // }
            ListFooterComponent={<EndOfList />}
            renderItem={({ item: product }) => (
              <Pressable
                onPress={() => navigation.navigate("StockDetail", product)}
              >
                <Div py={20} borderTopColor="grey" borderTopWidth={0.8} row>
                  <Div flex={5}>
                    <Text textAlign="center">
                      {product?.product_unit?.name}
                    </Text>
                  </Div>
                  <Div flex={4}>
                    <Text textAlign="center">
                      {product?.product_unit?.product?.brand?.name}
                    </Text>
                  </Div>
                  <Div flex={3}>
                    <Text textAlign="center">{product.stock}</Text>
                  </Div>
                  <Div flex={3}>
                    <Text textAlign="center">{product.indent}</Text>
                  </Div>
                  <Div flex={3}>
                    <Text textAlign="center">{product.outstanding_order}</Text>
                  </Div>
                  <Div flex={3}>
                    <Text textAlign="center">
                      {product.outstanding_shipment}
                    </Text>
                  </Div>
                  <Div flex={3}>
                    <Text textAlign="center">{product?.real_stock}</Text>
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
