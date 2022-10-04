import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { FlatList } from "react-native"
import { Div } from "react-native-magnus"

import Error from "components/Error"
import Loading from "components/Loading"
import ProductCard from "components/ProductCard"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useProductModelList from "api/hooks/pos/product/useProductModelList"

import {
  MainTabParamList,
  ProductStackParamList,
} from "Router/MainTabParamList"

import { dataFromPaginated } from "helper/pagination"
import s from "helper/theme"

import { ProductModel } from "types/POS/Product/ProductModel"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ProductStackParamList, "Product">,
  BottomTabNavigationProp<MainTabParamList>
>

type PropTypes = {}

export default (props: PropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const {
    queries: [{ data: productModelsData }],
    meta: { isLoading, isError, isFetching, refetch },
  } = useMultipleQueries([useProductModelList()] as const)

  const productModels: ProductModel[] = dataFromPaginated(productModelsData)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <Div mb={20}>
      <Text fontSize={14} fontWeight="bold" px={20} mb={10}>
        New Arrival
      </Text>
      <FlatList
        horizontal
        data={productModels}
        keyExtractor={({ id }) => `product_${id}`}
        showsHorizontalScrollIndicator={false}
        bounces={false}
        contentContainerStyle={[s.pX20]}
        renderItem={({ item: productModel, index }) => (
          <ProductCard
            key={`product_${productModel.id}`}
            productModel={productModel}
            onPress={() =>
              navigation.navigate("ProductDetail", { id: productModel.id })
            }
            containerStyle={[s.mR10]}
          />
        )}
      />
    </Div>
  )
}
