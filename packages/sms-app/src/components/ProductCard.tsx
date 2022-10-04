import React from "react"
import { TouchableOpacity, Dimensions } from "react-native"
import { Div } from "react-native-magnus"

import Image from "components/Image"
import Text from "components/Text"

import { formatCurrency } from "helper"
import s from "helper/theme"

import { ProductModel } from "types/POS/Product/ProductModel"

type PropTypes = {
  productModel: ProductModel
  onPress: () => void
  imageWidth?: number
  containerStyle?: object
}

export default ({
  productModel,
  onPress,
  imageWidth = 0.3 * Dimensions.get("window").width,
  containerStyle = {},
}: PropTypes) => {
  return (
    <TouchableOpacity
      onPress={onPress}
      style={[{ alignItems: "center" }, containerStyle]}
    >
      <Image
        width={imageWidth}
        scalable
        source={{
          uri:
            productModel?.images?.length > 0
              ? productModel?.images[0].url
              : null,
        }}
        style={[s.mB10]}
      />
      <Div maxW={imageWidth}>
        <Text fontWeight="bold" textAlign="center" mb={5}>
          {productModel.name}
        </Text>
        <Text fontSize={10} textAlign="center">{`${formatCurrency(
          productModel.priceMin,
        )} s/d\n${formatCurrency(productModel.priceMax)}`}</Text>
      </Div>
    </TouchableOpacity>
  )
}
