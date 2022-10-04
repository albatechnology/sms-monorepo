import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import {
  FlatList,
  Pressable,
  RefreshControl,
  useWindowDimensions,
} from "react-native"
import { Div, Icon } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Image from "components/Image"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import usePaymentCategoryList from "api/hooks/payment/usePaymentCategoryList"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"
import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

import { PaymentCategory } from "types/Payment/PaymentCategory"

type CurrentScreenRouteProp = RouteProp<
  CustomerStackParamList,
  "PaymentPayCategorySelection"
>
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "PaymentPayCategorySelection">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const orderId = route?.params?.orderId ?? -1
  if (orderId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Main")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const { width: screenWidth } = useWindowDimensions()

  const {
    queries: [{ data: paymentCategoryPaginatedData }],
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      hasNextPage,
      isManualRefetching,
      manualRefetch,
      fetchNextPage,
      isFetchingNextPage,
    },
  } = useMultipleQueries([usePaymentCategoryList()] as const)

  const data: PaymentCategory[] = dataFromPaginated(
    paymentCategoryPaginatedData,
  )

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }
  return (
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
      keyExtractor={({ id }) => `paymentCategory${id}`}
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
          onPress={() =>
            navigation.navigate("PaymentPayTypeSelection", {
              orderId,
              paymentCategoryId: item.id,
            })
          }
        >
          <Div
            p={20}
            bg="white"
            row
            justifyContent="space-between"
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            <Div row>
              <Image
                source={{
                  uri: item?.images?.length > 0 ? item?.images[0].url : null,
                }}
                width={0.25 * screenWidth}
                scalable
              />
              <Text ml={10} fontSize={14} fontWeight="bold" mb={5}>
                {item.name}
              </Text>
            </Div>
            <Icon
              bg="white"
              p={5}
              name="chevron-forward"
              color="primary"
              fontSize={18}
              fontFamily="Ionicons"
            />
          </Div>
        </Pressable>
      )}
    />
  )
}
