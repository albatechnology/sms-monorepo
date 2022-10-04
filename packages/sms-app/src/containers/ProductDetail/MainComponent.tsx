import { useBackHandler } from "@react-native-community/hooks"
import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import { LinearGradient } from "expo-linear-gradient"
import React, { useState, useMemo, useCallback } from "react"
import { TouchableOpacity } from "react-native"
import { FlatList } from "react-native-gesture-handler"
import { Button, Div } from "react-native-magnus"

import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Text from "components/Text"

import useMultipleQueries, {
  MultipleQueriesMetaReturnType,
} from "hooks/useMultipleQueries"

import { useCart } from "providers/Cart"

import useProductUnitList from "api/hooks/pos/productUnit/useProductUnitList"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  MainTabParamList,
  ProductStackParamList,
} from "Router/MainTabParamList"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_DISABLED } from "helper/theme"

import { ProductModel } from "types/POS/Product/ProductModel"

import ProductSelectionCard from "./ProductSelectionCard"
import ProductSelectionModal, {
  ProductUnitSelection,
} from "./ProductSelectionModal"
import TopPart from "./TopPart"
import TotalFooter from "./TotalFooter"

type PropTypes = {
  queryMeta: MultipleQueriesMetaReturnType
  productModel: ProductModel
}

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ProductStackParamList, "ProductDetail">,
  CompositeNavigationProp<
    BottomTabNavigationProp<MainTabParamList>,
    StackNavigationProp<EntryStackParamList>
  >
>

export default ({ queryMeta, productModel }: PropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [modalVisible, setModalVisible] = useState(false)
  const [rawProductUnitSelections, setProductUnitSelections] = useState<
    ProductUnitSelection[]
  >([])

  const productUnitSelections = rawProductUnitSelections.sort(
    (a, b) => a.productUnitId - b.productUnitId,
  )

  const {
    queries: [{ data: productUnitsData }],
    meta,
  } = useMultipleQueries([
    useProductUnitList({
      filterId: productUnitSelections.map((x) => x.productUnitId).join(","),
    }),
  ] as const)

  const productUnits = dataFromPaginated(productUnitsData)
  const { addItem } = useCart()

  useBackHandler(() => {
    if (modalVisible) {
      setModalVisible(false)
      return true
    }
    return false
  })
  const totalPrice = useMemo(() => {
    if (!productUnits) return null

    return productUnitSelections.reduce((acc, x) => {
      return (
        acc +
        productUnits.find((productUnit) => productUnit.id === x.productUnitId)
          .price *
          x.quantity
      )
    }, 0)
  }, [productUnitSelections, productUnits])

  const onAddSelection = useCallback(
    (newProductUnitSelection: ProductUnitSelection) => {
      setProductUnitSelections((prevSelections) => {
        const previousObj = prevSelections.find(
          (prevSelection) =>
            prevSelection.productUnitId ===
            newProductUnitSelection.productUnitId,
        )
        if (!!previousObj) {
          return prevSelections
            .filter(
              (x) => x.productUnitId !== newProductUnitSelection.productUnitId,
            )
            .concat({
              ...newProductUnitSelection,
              quantity: previousObj.quantity + newProductUnitSelection.quantity,
            })
        } else {
          return prevSelections.concat(newProductUnitSelection)
        }
      })
    },
    [],
  )

  const onAddToCart = useCallback(() => {
    if (productUnitSelections.length === 0) {
      toast("Mohon pilih produk")
      return
    }
    addItem(
      productUnitSelections.map((selection) => ({
        productUnitId: selection.productUnitId,
        quantity: selection.quantity,
        productUnitData: productUnits?.find(
          (x) => x.id === selection.productUnitId,
        ),
      })),
    )
    navigation.navigate("Cart")
  }, [addItem, navigation, productUnitSelections, productUnits])

  const onMinus = useCallback(
    (productUnitId) => {
      const selectedProductUnit = productUnitSelections.find(
        (x) => x.productUnitId === productUnitId,
      )

      const theRest = productUnitSelections.filter(
        (x) => x.productUnitId !== productUnitId,
      )

      setProductUnitSelections([
        ...theRest,
        {
          productUnitId: productUnitId,
          quantity: selectedProductUnit.quantity - 1,
        },
      ])
    },
    [productUnitSelections],
  )

  const onPlus = useCallback(
    (productUnitId) => {
      const selectedProductUnit = productUnitSelections.find(
        (x) => x.productUnitId === productUnitId,
      )

      const theRest = productUnitSelections.filter(
        (x) => x.productUnitId !== productUnitId,
      )

      setProductUnitSelections([
        ...theRest,
        {
          productUnitId: productUnitId,
          quantity: selectedProductUnit.quantity + 1,
        },
      ])
    },
    [productUnitSelections],
  )

  const onUpdateQuantity = useCallback(
    (productUnitId, qty) => {
      const theRest = productUnitSelections.filter(
        (x) => x.productUnitId !== productUnitId,
      )

      setProductUnitSelections([
        ...theRest,
        {
          productUnitId: productUnitId,
          quantity: qty,
        },
      ])
    },
    [productUnitSelections],
  )

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <ProductSelectionModal
        modalVisible={modalVisible}
        setModalVisible={setModalVisible}
        productModel={productModel}
        onAddSelection={onAddSelection}
      />
      <FlatList
        contentContainerStyle={{ flexGrow: 1, backgroundColor: "white" }}
        data={productUnitSelections}
        keyExtractor={(item, index) => `product_selection_${index}`}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={
          <>
            <TopPart
              productModel={productModel}
              onProductSelect={() => setModalVisible(true)}
            />
            {Object.keys(productUnitSelections).length > 0 && (
              <Text
                bg="white"
                px={20}
                pb={10}
                fontSize={14}
                fontWeight="bold"
                textDecorLine="underline"
              >
                Selected:
              </Text>
            )}
          </>
        }
        bounces={false}
        renderItem={({ item: productUnitSelection, index }) => {
          return (
            <ProductSelectionCard
              productUnit={productUnits?.find(
                (x) => x.id === productUnitSelection.productUnitId,
              )}
              quantity={productUnitSelection.quantity}
              onMinus={() => onMinus(productUnitSelection.productUnitId)}
              onPlus={() => onPlus(productUnitSelection.productUnitId)}
              onUpdateQuantity={(val) =>
                onUpdateQuantity(productUnitSelection.productUnitId, val)
              }
            />
          )
        }}
        ListFooterComponent={
          <Div
            p={20}
            bg="white"
            borderTopColor={COLOR_DISABLED}
            borderTopWidth={5}
          >
            <Div mb={20}>
              <Text fontSize={14} fontWeight="bold" mb={5}>
                Description :
              </Text>
              <Text mb={5}>{productModel.description}</Text>
            </Div>
          </Div>
        }
      />
      <TotalFooter
        totalPrice={totalPrice}
        buttonComponents={
          <>
            <TouchableOpacity onPress={onAddToCart}>
              <LinearGradient
                style={{
                  paddingVertical: 10,
                  paddingHorizontal: 20,
                  justifyContent: "center",
                  borderRadius: 4,
                }}
                locations={[0.5, 1.0]}
                colors={["#20B5C0", "#17949D"]}
              >
                <Text color="white" fontSize={14} textAlign="center">
                  + Cart
                </Text>
              </LinearGradient>
            </TouchableOpacity>
          </>
        }
      />
    </CustomKeyboardAvoidingView>
  )
}
