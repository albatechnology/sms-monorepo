import { HeaderBackButton } from "@react-navigation/stack"
import Case from "case"
import React, { useState } from "react"
import { FlatList, TouchableOpacity, Pressable } from "react-native"
import { Div, DivProps, Icon, Modal } from "react-native-magnus"
import { UseInfiniteQueryResult } from "react-query"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Loading from "components/Loading"
import Text from "components/Text"

import { CustomAxiosErrorType } from "api/errors"

import { Paginated, dataFromPaginated } from "helper/pagination"
import s, { COLOR_DISABLED } from "helper/theme"

import { ProductUnitCovering } from "types/POS/ProductUnit/ProductUnitCovering"

type PropTypes = {
  query: UseInfiniteQueryResult<Paginated<any>, CustomAxiosErrorType>
  onSelect: (productUnitCoveringId: number) => void
  selectedProductUnitCoveringId: number | null
}

export default ({
  query,
  onSelect,
  selectedProductUnitCoveringId,
}: PropTypes) => {
  const [modalVisible, setModalVisible] = useState(false)
  const hideModal = () => setModalVisible(false)
  const showModal = () => setModalVisible(true)

  const productUnitCoverings: ProductUnitCovering[] = dataFromPaginated(
    query?.data,
  )

  const selectedProductUnitCovering = selectedProductUnitCoveringId
    ? productUnitCoverings.find((x) => x.id === selectedProductUnitCoveringId)
    : null

  return (
    <Pressable onPress={() => showModal()}>
      <Div
        borderBottomColor={COLOR_DISABLED}
        borderBottomWidth={5}
        p={20}
        bg="white"
      >
        <Text fontSize={14} fontWeight="bold" textDecorLine="underline" mb={10}>
          Covering
        </Text>
        {selectedProductUnitCoveringId ? (
          <ProductUnitCoveringCard
            p={0}
            productUnitCovering={selectedProductUnitCovering}
            onPress={showModal}
          />
        ) : (
          <Text color="grey">Select a covering</Text>
        )}
        <Modal
          useNativeDriver
          isVisible={modalVisible}
          onBackdropPress={hideModal}
          animationIn={"slideInRight"}
          animationOut={"slideOutDown"}
          onBackButtonPress={hideModal}
          onDismiss={hideModal}
          onModalHide={hideModal}
          h="90%"
        >
          <Div
            zIndex={5}
            shadow="sm"
            py={16}
            bg="white"
            flexDir="row"
            alignItems="center"
          >
            <HeaderBackButton onPress={hideModal} style={[s.mR10]} />
            <Text fontSize={16} fontWeight="bold">
              Select Covering
            </Text>
          </Div>
          {query.isLoading && <Loading />}
          <FlatList
            data={productUnitCoverings}
            keyExtractor={(item, index) => `product_unit_covering_${index}`}
            showsVerticalScrollIndicator={false}
            bounces={false}
            onEndReachedThreshold={0.2}
            onEndReached={() => {
              if (query.hasNextPage) query.fetchNextPage()
            }}
            ListFooterComponent={() =>
              !!productUnitCoverings &&
              productUnitCoverings.length > 0 &&
              (query.isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
            }
            renderItem={({ item: productUnitCovering, index }) => {
              return (
                <ProductUnitCoveringCard
                  productUnitCovering={productUnitCovering}
                  borderBottomWidth={0.8}
                  borderBottomColor={COLOR_DISABLED}
                  onPress={() => {
                    onSelect(productUnitCovering.id)
                    hideModal()
                  }}
                />
              )
            }}
          />
        </Modal>
      </Div>
    </Pressable>
  )
}

type ProductUnitCoveringCardPropTypes = {
  productUnitCovering: ProductUnitCovering
  onPress?: () => void
} & DivProps

const ProductUnitCoveringCard = ({
  productUnitCovering,
  onPress = () => {},
  ...rest
}: ProductUnitCoveringCardPropTypes) => {
  return (
    <TouchableOpacity onPress={onPress}>
      <Div
        flex={1}
        p={20}
        bg="white"
        {...rest}
        row
        justifyContent="space-between"
      >
        <Div flex={1}>
          <Text fontSize={14} fontWeight="bold" mb={5}>
            {Case.title(productUnitCovering.name)}
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
    </TouchableOpacity>
  )
}
