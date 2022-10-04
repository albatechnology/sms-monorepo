import React from "react"
import { useWindowDimensions } from "react-native"
import { Div } from "react-native-magnus"

import Image from "components/Image"
import Text from "components/Text"

import { formatCurrency } from "helper"
import s from "helper/theme"

import QuantitySelector from "./QuantitySelector"

export default ({
  productCartData,
  zeroQuantityAllowed = false,
  onMinus,
  onPlus,
  onUpdateQuantity,
  ...rest
}) => {
  const { width: screenWidth } = useWindowDimensions()

  const reduceButtonEnabled =
    productCartData.quantity > (zeroQuantityAllowed ? 0 : 1)

  return (
    <Div row px={20} pb={20} alignItems="center" bg="white">
      <Image
        style={[
          {
            width: 0.25 * screenWidth,
            height: undefined,
            aspectRatio: 1,
            resizeMode: "contain",
          },
          s.mR10,
        ]}
        source={{ uri: productCartData.img }}
      />
      <Div row flex={1} justifyContent="space-between">
        <Div w={0.33 * screenWidth}>
          <Text fontWeight="bold" mb={5}>
            {productCartData.name}
          </Text>
          <Text>{formatCurrency(productCartData.price)}</Text>
        </Div>

        <QuantitySelector
          quantity={productCartData.quantity}
          onMinus={onMinus}
          onPlus={onPlus}
          onUpdateQuantity={onUpdateQuantity}
          disableMinus={!reduceButtonEnabled}
        />
      </Div>
    </Div>
  )
}
