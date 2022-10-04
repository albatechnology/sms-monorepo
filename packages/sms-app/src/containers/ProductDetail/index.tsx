import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"

import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import { customErrorHandler } from "api/errors"
import useProductModelById from "api/hooks/pos/product/useProductModelById"

import {
  MainTabParamList,
  ProductStackParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"

import MainComponent from "./MainComponent"

type CurrentScreenRouteProp = RouteProp<ProductStackParamList, "ProductDetail">
type CurrentScreenNavigationProp = CompositeNavigationProp<
  BottomTabNavigationProp<MainTabParamList>,
  StackNavigationProp<ProductStackParamList, "ProductDetail">
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const productModelId = route?.params?.id ?? -1
  if (productModelId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: productModelData }],
    meta,
  } = useMultipleQueries([
    useProductModelById(
      productModelId,
      customErrorHandler({
        404: () => {
          toast("Product tidak ditemukan")
          if (navigation.canGoBack()) {
            navigation.goBack()
          } else {
            navigation.navigate("Dashboard")
          }
        },
      }),
    ),
  ] as const)
  const { isError, isLoading, isFetching, refetch } = meta

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return <MainComponent queryMeta={meta} productModel={productModelData} />
}
