import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { FlatList, useWindowDimensions } from "react-native"

import Image from "components/Image"
import ProductCard from "components/ProductCard"
import Text from "components/Text"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  ProductStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import s from "helper/theme"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<ProductStackParamList, "ProductByBrand">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const { width: screenWidth } = useWindowDimensions()

  return (
    <FlatList
      contentContainerStyle={[{ flexGrow: 1 }, s.bgWhite]}
      data={[]}
      keyExtractor={({ name }) => `model_${name}`}
      showsVerticalScrollIndicator={false}
      bounces={false}
      numColumns={2}
      columnWrapperStyle={[s.pX20, s.pY20, { justifyContent: "space-between" }]}
      ListHeaderComponent={
        <Image
          width={screenWidth}
          scalable
          source={require("assets/banner_cafe.jpg")}
        />
      }
      ListEmptyComponent={
        <Text fontSize={14} textAlign="center" p={20}>
          Kosong
        </Text>
      }
      onEndReachedThreshold={0.2}
      // onEndReached={() => {
      //   if (hasNextPage) fetchNextPage()
      // }}
      // ListFooterComponent={() =>
      //   !!data &&
      //   data.length > 0 &&
      //   (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
      // }
      renderItem={({ item: productModel, index }) => (
        <ProductCard
          key={`model_${productModel.id}`}
          productModel={productModel}
          onPress={() =>
            navigation.navigate("ProductDetail", { id: productModel.id })
          }
          imageWidth={0.4 * screenWidth}
        />
      )}
    />
  )
}
