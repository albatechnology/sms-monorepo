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

import { ProductUnitColor } from "types/POS/ProductUnit/ProductUnitColor"

type PropTypes = {
  query: UseInfiniteQueryResult<Paginated<any>, CustomAxiosErrorType>
  onSelect: (productUnitColorId: number) => void
  selectedProductUnitColorId: number | null
}

export default ({ query, onSelect, selectedProductUnitColorId }: PropTypes) => {
  const [modalVisible, setModalVisible] = useState(false)
  const hideModal = () => setModalVisible(false)
  const showModal = () => setModalVisible(true)

  const productUnitColors: ProductUnitColor[] = dataFromPaginated(query?.data)

  const selectedProductUnitColor = selectedProductUnitColorId
    ? productUnitColors.find((x) => x.id === selectedProductUnitColorId)
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
          Colour
        </Text>
        {selectedProductUnitColorId ? (
          <ProductUnitColorCard
            p={0}
            productUnitColor={selectedProductUnitColor}
            onPress={showModal}
          />
        ) : (
          <Text color="grey">Select a colour</Text>
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
              Select Color
            </Text>
          </Div>
          {query.isLoading && <Loading />}
          <FlatList
            data={productUnitColors}
            keyExtractor={(item, index) =>
              `product_category_code_selection_${index}`
            }
            showsVerticalScrollIndicator={false}
            bounces={false}
            onEndReachedThreshold={0.2}
            onEndReached={() => {
              if (query.hasNextPage) query.fetchNextPage()
            }}
            ListFooterComponent={() =>
              !!productUnitColors &&
              productUnitColors.length > 0 &&
              (query.isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
            }
            renderItem={({ item: productUnitColor, index }) => {
              return (
                <ProductUnitColorCard
                  productUnitColor={productUnitColor}
                  borderBottomWidth={0.8}
                  borderBottomColor={COLOR_DISABLED}
                  onPress={() => {
                    onSelect(productUnitColor.id)
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

type ProductUnitColorCardPropTypes = {
  productUnitColor: ProductUnitColor
  onPress?: () => void
} & DivProps

const ProductUnitColorCard = ({
  productUnitColor,
  onPress = () => {},
  ...rest
}: ProductUnitColorCardPropTypes) => {
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
            {Case.title(productUnitColor.name)}
          </Text>
          {/* <Text>Code: {Case.title(productUnitColor.code)}</Text> */}
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
