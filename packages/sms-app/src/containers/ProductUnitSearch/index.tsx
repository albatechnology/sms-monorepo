import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import { LinearGradient } from "expo-linear-gradient"
import React, { useState } from "react"
import { FlatList, TouchableOpacity } from "react-native"
import { Button, Div } from "react-native-magnus"

import FooterLoading from "components/CommonList/FooterLoading"
import NotFound from "components/CommonList/NotFound"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { useCart } from "providers/Cart"

import ProductUnitFilter from "filters/ProductUnitFilter"

import useProductUnitList from "api/hooks/pos/productUnit/useProductUnitList"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  ProductStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatCurrency } from "helper"
import { dataFromPaginated } from "helper/pagination"
import s from "helper/theme"

import { ProductUnit } from "types/POS/ProductUnit/ProductUnit"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ProductStackParamList, "ProductUnitSearch">,
  CompositeNavigationProp<
    BottomTabNavigationProp<MainTabParamList>,
    StackNavigationProp<EntryStackParamList>
  >
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const { addItem } = useCart()

  const [filters, setFilter] = useState({})

  const {
    queries: [{ data: modelPaginatedData }],
    meta: {
      isLoading,
      isFetchingNextPage,
      hasNextPage,
      fetchNextPage,
      isError,
      isFetching,
      refetch,
    },
  } = useMultipleQueries([useProductUnitList({ ...filters })] as const)

  const data: ProductUnit[] = dataFromPaginated(modelPaginatedData)
  const onAddToCard = (item) => {
    addItem([
      {
        productUnitId: item.id,
        quantity: 1,
        productUnitData: item,
      },
    ])
    navigation.navigate("Cart")
  }

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  return (
    <>
      <ProductUnitFilter activeFilterValues={filters} onSetFilter={setFilter} />
      <FlatList
        contentContainerStyle={[{ flexGrow: 1 }, s.p20, s.bgWhite]}
        data={data}
        keyExtractor={({ name, id }) => `product_unit_${name}_${id}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListEmptyComponent={() => {
          if (isLoading) {
            return <Loading />
          } else {
            return <NotFound />
          }
        }}
        onEndReachedThreshold={0.2}
        onEndReached={() => {
          if (hasNextPage) fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : null)
        }
        renderItem={({ item, index }) => (
          <Div flex={1} py={20} borderBottomWidth={0.8} borderColor="grey" row>
            <Div flex={1} mr={10}>
              <Text mb={5}>{item.name}</Text>
              <Text>{formatCurrency(item.price)}</Text>
            </Div>
            <TouchableOpacity
              onPress={() => {
                onAddToCard(item)
              }}
            >
              <LinearGradient
                style={{
                  paddingVertical: 10,
                  paddingHorizontal: 20,
                  justifyContent: "center",
                  alignSelf: "center",
                  borderRadius: 4,
                }}
                locations={[0.5, 1.0]}
                colors={["#20B5C0", "#17949D"]}
              >
                <Text color="white">+ Cart</Text>
              </LinearGradient>
            </TouchableOpacity>
          </Div>
        )}
      />
    </>
  )
}
