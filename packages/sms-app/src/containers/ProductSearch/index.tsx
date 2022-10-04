import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useState } from "react"
import { FlatList, useWindowDimensions } from "react-native"

import FooterLoading from "components/CommonList/FooterLoading"
import NotFound from "components/CommonList/NotFound"
import Loading from "components/Loading"
import ProductCard from "components/ProductCard"

import useMultipleQueries from "hooks/useMultipleQueries"

import ProductFilter from "filters/ProductFilter"

import useProductModelList from "api/hooks/pos/product/useProductModelList"

import {
  ProductStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { dataFromPaginated } from "helper/pagination"
import s from "helper/theme"

import { ProductModel } from "types/POS/Product/ProductModel"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ProductStackParamList, "Product">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const { width: screenWidth } = useWindowDimensions()

  const [filters, setFilter] = useState({})

  const {
    queries: [{ data: modelPaginatedData }],
    meta: { isLoading, isFetchingNextPage, hasNextPage, fetchNextPage },
  } = useMultipleQueries([useProductModelList({ ...filters })] as const) //DEBT: sort by name

  const data: ProductModel[] = dataFromPaginated(modelPaginatedData)

  return (
    <>
      <ProductFilter activeFilterValues={filters} onSetFilter={setFilter} />
      <FlatList
        contentContainerStyle={[{ flexGrow: 1 }, s.p20, s.bgWhite]}
        data={data}
        keyExtractor={({ name }) => `model_${name}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        numColumns={2}
        columnWrapperStyle={[s.pB20, { justifyContent: "space-between" }]}
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
          <ProductCard
            imageWidth={0.4 * screenWidth}
            productModel={item}
            onPress={() =>
              navigation.navigate("ProductDetail", { id: item.id })
            }
          />
        )}
      />
    </>
  )
}
