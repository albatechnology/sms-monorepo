import { LinearGradient } from "expo-linear-gradient"
import React, { useCallback, useState } from "react"
import { ScrollView, TouchableOpacity } from "react-native"
import { Button, Div, Modal } from "react-native-magnus"

import Error from "components/Error"
import Loading from "components/Loading"
import QuantitySelector from "components/QuantitySelector"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useProductCategoryCodeList from "api/hooks/pos/product/useProductCategoryCodeList"
import useProductList from "api/hooks/pos/product/useProductList"
import useProductVersionList from "api/hooks/pos/product/useProductVersionList"
import useProductUnitColorList from "api/hooks/pos/productUnit/useProductUnitColorList"
import useProductUnitCoveringList from "api/hooks/pos/productUnit/useProductUnitCoveringList"
import useProductUnitList from "api/hooks/pos/productUnit/useProductUnitList"

import { dataFromPaginated } from "helper/pagination"

import { ProductModel } from "types/POS/Product/ProductModel"

import TotalFooter from "../TotalFooter"
import ProductCategoryCodeSelection from "./ProductCategoryCodeSelection"
import ProductUnitColorSelection from "./ProductUnitColorSelection"
import ProductUnitCoveringSelection from "./ProductUnitCoveringSelection"
import ProductVersionSelection from "./ProductVersionSelection"

export type ProductUnitSelection = {
  quantity: number
  productUnitId: number
}

type PropTypes = {
  modalVisible: boolean
  setModalVisible: React.Dispatch<React.SetStateAction<boolean>>
  productModel: ProductModel
  onAddSelection: (productUnitSelection: ProductUnitSelection) => void
}

export default ({
  modalVisible,
  setModalVisible,
  productModel,
  onAddSelection,
}: PropTypes) => {
  const hideModal = useCallback(() => setModalVisible(false), [setModalVisible])

  const onHandleAddSelection = useCallback(
    (productUnitSelection) => {
      hideModal()
      onAddSelection(productUnitSelection)
    },
    [hideModal, onAddSelection],
  )

  return (
    <Modal
      useNativeDriver
      isVisible={modalVisible}
      onBackdropPress={hideModal}
      animationIn={"slideInUp"}
      onBackButtonPress={hideModal}
      onDismiss={hideModal}
      onModalHide={hideModal}
      h="90%"
    >
      <Div zIndex={5} shadow="sm" p={20} bg="white">
        <Text fontSize={16} fontWeight="bold">
          Product Options
        </Text>
      </Div>
      <MainComponent
        productModel={productModel}
        onAddSelection={onHandleAddSelection}
      />
    </Modal>
  )
}

type MainComponentPropTypes = {
  productModel: ProductModel
  onAddSelection: PropTypes["onAddSelection"]
}

const MainComponent = ({
  productModel,
  onAddSelection,
}: MainComponentPropTypes) => {
  const [productVersionId, setProductVersionId] = useState<number>(null)
  const [productCategoryCodeId, setProductCategoryCodeId] =
    useState<number>(null)

  const [productUnitColorId, setProductUnitColorId] = useState<number>(null)
  const [productUnitCoveringId, setProductUnitCoveringId] =
    useState<number>(null)

  const [quantity, setQuantity] = useState(1)

  const {
    queries: [
      productVersionsQuery,
      productCategoryCodesQuery,
      { data: productsData },
    ],
    meta: {
      isError: productQueriesIsError,
      isLoading: productQueriesIsLoading,
      isFetching: productQueriesIsFetching,
      refetch: productQueriesRefetch,
    },
  } = useMultipleQueries(
    [
      useProductVersionList({
        filterProductModelId: productModel.id.toString(),
      }),
      useProductCategoryCodeList(
        {
          filterProductModelId: productModel.id.toString(),
          filterProductVersionId: productVersionId?.toString(),
        },
        10,
        { enabled: !!productVersionId },
      ),
      useProductList(
        {
          filterProductModelId: productModel.id.toString(),
          filterProductVersionId: productVersionId?.toString(),
          filterProductCategoryCodeId: productCategoryCodeId?.toString(),
        },
        1,
        { enabled: !!productVersionId && !!productCategoryCodeId },
      ),
    ] as const,
    { useStandardIsLoadingBehaviour: true },
  )

  const products = dataFromPaginated(productsData)
  const productId = products?.length > 0 ? products[0]?.id : null

  const {
    queries: [
      productUnitColorsQuery,
      productUnitCoveringsQuery,
      { data: productUnitsData },
    ],
    meta: {
      isError: productUnitQueriesIsError,
      isLoading: productUnitQueriesIsLoading,
      isFetching: productUnitQueriesIsFetching,
      refetch: productUnitQueriesRefetch,
    },
  } = useMultipleQueries(
    [
      useProductUnitColorList(
        {
          filterProductId: productId,
        },
        10,
        { enabled: !!productId },
      ),
      useProductUnitCoveringList({ filterProductId: productId }, 10, {
        enabled: !!productId,
      }),
      useProductUnitList(
        {
          filterProductId: productId,
          filterCoveringId: productUnitCoveringId,
          filterColourId: productUnitColorId,
        },
        1,
        {
          enabled:
            !!productId && !!productUnitCoveringId && !!productUnitColorId,
        },
      ),
    ] as const,
    { useStandardIsLoadingBehaviour: true },
  )

  const productUnits = dataFromPaginated(productUnitsData)
  const productUnit = productUnits?.length > 0 ? productUnits[0] : null
  const productUnitId = productUnit ? productUnit?.id : null
  const productUnitPrice = productUnit ? productUnit?.price : null

  const refetchAll = () => {
    productQueriesRefetch()
    productUnitQueriesRefetch()
  }
  const isFetchingAny = productQueriesIsFetching || productUnitQueriesIsFetching
  const isErrorAny = productQueriesIsError || productUnitQueriesIsError
  const isLoadingAny = productQueriesIsLoading || productUnitQueriesIsLoading

  if (isErrorAny) {
    return <Error refreshing={isFetchingAny} onRefresh={refetchAll} />
  }

  const onAdd = () => {
    onAddSelection({ productUnitId, quantity })
  }
  return (
    <>
      <ScrollView style={{ flex: 1 }}>
        <ProductVersionSelection
          query={productVersionsQuery}
          onSelect={(val) => {
            setProductVersionId(val)
            setProductCategoryCodeId(null)
            setProductUnitColorId(null)
            setProductUnitCoveringId(null)
          }}
          selectedProductVersionId={productVersionId}
        />
        {!!productCategoryCodesQuery.data && (
          <ProductCategoryCodeSelection
            query={productCategoryCodesQuery}
            onSelect={(val) => {
              setProductCategoryCodeId(val)
              setProductUnitColorId(null)
              setProductUnitCoveringId(null)
            }}
            selectedProductCategoryCodeId={productCategoryCodeId}
          />
        )}
        {!!productId && (
          <ProductUnitCoveringSelection
            query={productUnitCoveringsQuery}
            onSelect={setProductUnitCoveringId}
            selectedProductUnitCoveringId={productUnitCoveringId}
          />
        )}
        {!!productId && !!productUnitCoveringId && (
          <ProductUnitColorSelection
            query={productUnitColorsQuery}
            onSelect={setProductUnitColorId}
            selectedProductUnitColorId={productUnitColorId}
          />
        )}
        {isLoadingAny && <Loading />}
      </ScrollView>
      <TotalFooter
        productUnit={productUnit}
        totalPrice={productUnitPrice * quantity}
        buttonComponents={
          <Div row alignItems="center">
            <QuantitySelector
              quantity={quantity}
              onMinus={() => setQuantity((q) => q - 1)}
              onPlus={() => setQuantity((q) => q + 1)}
              onUpdateQuantity={setQuantity}
              disableMinus={quantity <= 1}
            />
            <TouchableOpacity disabled={quantity < 1} onPress={onAdd}>
              <LinearGradient
                style={{
                  paddingVertical: 10,
                  paddingHorizontal: 20,
                  marginLeft: 5,
                  justifyContent: "center",
                  borderRadius: 4,
                }}
                locations={[0.5, 1.0]}
                colors={
                  !productUnitId || quantity < 1
                    ? ["#DADADA", "#DADADA"]
                    : ["#20B5C0", "#17949D"]
                }
              >
                <Text color="white" fontSize={14} textAlign="center">
                  + Add
                </Text>
              </LinearGradient>
            </TouchableOpacity>
          </Div>
        }
      />
    </>
  )
}
