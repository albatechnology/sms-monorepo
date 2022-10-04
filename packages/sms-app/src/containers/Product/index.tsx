import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useEffect, useState } from "react"
import {
  FlatList,
  Pressable,
  TouchableOpacity,
  useWindowDimensions,
} from "react-native"
import { Button, Div, Icon } from "react-native-magnus"
import Modal from "react-native-modal"

import FooterLoading from "components/CommonList/FooterLoading"
import Image from "components/Image"
import LeadDropdownInput from "components/LeadDropdownInput"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useBrandList from "api/hooks/pos/productCategorization/useBrandList"

import {
  ProductStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { responsive } from "helper"
import { dataFromPaginated } from "helper/pagination"
import s from "helper/theme"

import { Brand } from "types/POS/ProductCategorization/Brand"

import CafeButton from "./CafeButton"
import NewArrival from "./NewArrival"
import ScanQR from "./ScanQR"
import StockButton from "./StockButton"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ProductStackParamList, "Product">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const { width: screenWidth } = useWindowDimensions()

  const [isModalVisible, setIsModalVisible] = useState(false)

  const {
    queries: [{ data: brandPaginatedData }],
    meta: { isLoading, isFetchingNextPage, hasNextPage, fetchNextPage },
  } = useMultipleQueries([useBrandList()] as const) //DEBT: sort by name

  useEffect(() => {
    navigation.setOptions({
      headerLeft: () => (
        <Pressable onPress={() => setIsModalVisible(true)}>
          <Icon
            name="search"
            color="white"
            fontSize={responsive(15)}
            fontFamily="Ionicons"
            px={20}
          />
        </Pressable>
      ),
    })
  }, [navigation])

  const data: Brand[] = dataFromPaginated(brandPaginatedData)
  return (
    <>
      <Modal
        useNativeDriver
        isVisible={isModalVisible}
        animationIn="slideInUp"
        animationOut="slideOutDown"
        onBackdropPress={() => setIsModalVisible(false)}
      >
        <Div p={20} bg="white">
          <Text fontSize={14} mb={20} textDecorLine="underline">
            Search Method:
          </Text>
          <Button
            onPress={() => {
              setIsModalVisible(false)
              navigation.navigate("ProductSearch")
            }}
            bg="white"
            borderColor="primary"
            borderWidth={0.8}
            mb={10}
            px={20}
            alignSelf="center"
            w={"100%"}
          >
            <Text>Search By Model</Text>
          </Button>
          <Button
            onPress={() => {
              setIsModalVisible(false)
              navigation.navigate("ProductUnitSearch")
            }}
            bg="primary"
            mb={10}
            px={20}
            alignSelf="center"
            w={"100%"}
          >
            <Text color="white">Search By Product Unit</Text>
          </Button>
        </Div>
      </Modal>
      <FlatList
        contentContainerStyle={[{ flexGrow: 1 }, s.bgWhite]}
        data={data}
        keyExtractor={({ name }) => `category_${name}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        numColumns={2}
        columnWrapperStyle={[
          s.pX20,
          s.pB20,
          { justifyContent: "space-between" },
        ]}
        ListHeaderComponent={
          <Div>
            <Image
              width={screenWidth}
              scalable
              source={require("assets/banner_product.png")}
            />
            <Div
              flex={1}
              px={20}
              top={-20}
              mb={10}
              row
              justifyContent="space-between"
            >
              <ScanQR />
              <CafeButton navigate={() => navigation.navigate("Cafe")} />
            </Div>
            <Div px={20} pb={20}>
              <StockButton
                navigate={() => navigation.navigate("StockSelectChannel")}
              />
            </Div>

            <NewArrival />
            <Div row justifyContent="space-between" px={20} mb={10}>
              <Text fontSize={14} fontWeight="bold">
                All Brand
              </Text>
            </Div>
          </Div>
        }
        ListEmptyComponent={() => {
          if (isLoading) {
            return <Loading />
          } else {
            return (
              <Text fontSize={14} textAlign="center" p={20}>
                Kosong
              </Text>
            )
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
        renderItem={({ item, index }) => <BrandCard item={item} />}
      />
    </>
  )
}

const BrandCard = ({ item }: { item: Brand }) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const { width: screenWidth } = useWindowDimensions()

  return (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("ProductByBrand", {
          id: item.id,
          brandName: item.name,
        })
      }
    >
      <Div>
        <Image
          width={0.4 * screenWidth}
          scalable
          source={{ uri: item?.images?.length > 0 ? item.images[0].url : null }}
          style={[s.mB10]}
        />
        <Text
          maxW={0.4 * screenWidth}
          fontWeight="bold"
          textAlign="center"
          mb={5}
          numberOfLines={3}
        >
          {item.name}
        </Text>
      </Div>
    </TouchableOpacity>
  )
}
