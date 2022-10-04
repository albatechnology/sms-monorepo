import { useRoute } from "@react-navigation/native"
import React, { useEffect, useState } from "react"
import { FlatList, RefreshControl, ScrollView, View } from "react-native"
import { Div } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"
import { string } from "yup/lib/locale"

import Text from "components/Text"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

import theme from "helper/theme"

import standardErrorHandling from "../../api/errors"

const StockDetail = () => {
  const route = useRoute()
  const params = route.params
  const { loggedIn } = useAuth()
  const axios = useAxios()
  const [data, setData] = useState([])
  const queryData = useQuery<string, any>(["ManualTransfer", loggedIn], () => {
    return axios
      .get(
        `stocks/extended/detail/${params.company_id}/${params.channel.id}/${params.product_unit_id}`,
      )
      .then((res) => {
        setData(res.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })
  return (
    <>
      <View style={[theme.mL10, theme.mT10]}>
        <Text fontWeight="bold" fontSize={16}>
          {params?.channel.name}
        </Text>
        <Text fontSize={14}>{params?.product_unit?.name}</Text>
      </View>
      <ScrollView
        bounces={false}
        horizontal
        showsHorizontalScrollIndicator={false}
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
            theme.mT20,
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
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Created At
                </Text>
              </Div>
              <Div flex={5}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Invoice Number
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Sales
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Deal at
                </Text>
              </Div>
              <Div flex={3}>
                <Text color="white" fontWeight="bold" textAlign="center">
                  Expected Delivery At
                </Text>
              </Div>
            </Div>
          }
          // ListFooterComponent={() =>
          //   !!data &&
          //   data.length > 0 &&
          //   (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
          // }
          renderItem={({ item: product }) => (
            <Div py={20} borderTopColor="grey" borderTopWidth={0.8} row>
              <Div flex={3}>
                <Text textAlign="center">{`${product?.created_at}`}</Text>
              </Div>
              <Div flex={5}>
                <Text textAlign="center">{product.invoice_number}</Text>
              </Div>
              <Div flex={3}>
                <Text textAlign="center">{product.sales}</Text>
              </Div>
              <Div flex={3}>
                <Text textAlign="center">{product.deal_at}</Text>
              </Div>
              <Div flex={3}>
                <Text textAlign="center">
                  {product.expected_shipping_datetime}
                </Text>
              </Div>
            </Div>
          )}
        />
      </ScrollView>
    </>
  )
}

export default StockDetail
