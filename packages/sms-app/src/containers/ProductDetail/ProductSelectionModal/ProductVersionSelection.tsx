import { HeaderBackButton } from "@react-navigation/stack"
import Case from "case"
import React, { useState } from "react"
import { useWindowDimensions } from "react-native"
import { FlatList, Pressable, TouchableOpacity } from "react-native"
import { Div, DivProps, Icon, Modal } from "react-native-magnus"
import { UseInfiniteQueryResult } from "react-query"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Image from "components/Image"
import Loading from "components/Loading"
import Text from "components/Text"

import { CustomAxiosErrorType } from "api/errors"

import { Paginated, dataFromPaginated } from "helper/pagination"
import s, { COLOR_DISABLED } from "helper/theme"

import { ProductVersion } from "types/POS/Product/ProductVersion"

type PropTypes = {
  query: UseInfiniteQueryResult<Paginated<any>, CustomAxiosErrorType>
  onSelect: (productVersionId: number) => void
  selectedProductVersionId: number | null
}

export default ({ query, onSelect, selectedProductVersionId }: PropTypes) => {
  const [modalVisible, setModalVisible] = useState(false)
  const hideModal = () => setModalVisible(false)
  const showModal = () => setModalVisible(true)

  const productVersions: ProductVersion[] = dataFromPaginated(query?.data)

  const selectedProduct = productVersions?.find(
    (x) => x.id === selectedProductVersionId,
  )

  return (
    <Pressable onPress={() => showModal()}>
      <Div
        borderBottomColor={COLOR_DISABLED}
        borderBottomWidth={5}
        p={20}
        bg="white"
      >
        <Text fontSize={14} fontWeight="bold" textDecorLine="underline" mb={10}>
          Version
        </Text>
        {selectedProductVersionId ? (
          <ProductVersionCard
            p={0}
            productVersion={selectedProduct}
            onPress={showModal}
          />
        ) : (
          <Text color="grey">Select a version</Text>
        )}
        <Modal
          useNativeDriver
          isVisible={modalVisible}
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
              Select Version
            </Text>
          </Div>
          {query.isLoading && <Loading />}
          <FlatList
            data={productVersions}
            keyExtractor={(item, index) => `product_version_selection_${index}`}
            showsVerticalScrollIndicator={false}
            bounces={false}
            onEndReachedThreshold={0.2}
            onEndReached={() => {
              if (query.hasNextPage) query.fetchNextPage()
            }}
            ListFooterComponent={() =>
              !!productVersions &&
              productVersions.length > 0 &&
              (query.isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
            }
            renderItem={({ item: productVersion, index }) => {
              return (
                <ProductVersionCard
                  productVersion={productVersion}
                  borderBottomWidth={0.8}
                  borderBottomColor={COLOR_DISABLED}
                  onPress={() => {
                    onSelect(productVersion.id)
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

type ProductVersionCardPropTypes = {
  productVersion: ProductVersion
  onPress?: () => void
} & DivProps

const ProductVersionCard = ({
  productVersion,
  onPress = () => {},
  ...rest
}: ProductVersionCardPropTypes) => {
  const { width: screenWidth } = useWindowDimensions()

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
        <Div flex={1} row>
          <Image
            width={0.25 * screenWidth}
            scalable
            source={{
              uri:
                productVersion?.images?.length > 0
                  ? productVersion.images[0].url
                  : null,
            }}
            style={[s.mR10]}
          />
          <Div flex={1} justifyContent="center">
            <Text fontSize={14} fontWeight="bold" mb={5}>
              {Case.title(productVersion.name)}
            </Text>
            <Text>
              Dimension {productVersion.width} x {productVersion.height} x{" "}
              {productVersion.length} cm
            </Text>
          </Div>
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
