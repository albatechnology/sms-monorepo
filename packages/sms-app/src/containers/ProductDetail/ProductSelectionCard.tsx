import React from "react"
import { Div } from "react-native-magnus"

import QuantitySelector from "components/QuantitySelector"
import Text from "components/Text"

import { formatCurrency } from "helper"

import { ProductUnit } from "types/POS/ProductUnit/ProductUnit"

type PropTypes = {
  productUnit: ProductUnit
  quantity: number
  onMinus: () => void
  onPlus: () => void
  onUpdateQuantity: (val) => void
}

export default ({
  productUnit,
  quantity,
  onMinus,
  onPlus,
  onUpdateQuantity,
}: PropTypes) => {
  const reduceButtonEnabled = quantity > 1

  if (!productUnit) {
    return null
  }

  return (
    <Div
      pb={20}
      px={20}
      flexDir="row"
      bg="white"
      alignItems="flex-start"
      justifyContent="space-between"
      flex={1}
    >
      <Div flex={1}>
        <Text fontWeight="bold" mb={5}>
          {productUnit.name}
        </Text>
        <Text fontWeight="bold" mb={5}>
          {productUnit.covering.name} - {productUnit.colour.name}
        </Text>
        <Text>{formatCurrency(productUnit.price)}</Text>
      </Div>

      <QuantitySelector
        quantity={quantity}
        onMinus={onMinus}
        onPlus={onPlus}
        onUpdateQuantity={onUpdateQuantity}
        disableMinus={!reduceButtonEnabled}
      />
    </Div>
  )
}
