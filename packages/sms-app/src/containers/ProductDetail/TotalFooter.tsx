import React from "react"
import { Div } from "react-native-magnus"

import Text from "components/Text"

import { formatCurrency } from "helper"

import { ProductUnit } from "types/POS/ProductUnit/ProductUnit"

type PropTypes = {
  totalPrice: number
  buttonComponents: React.ReactNode
  productUnit: ProductUnit
}

export default function TotalFooter({
  totalPrice,
  buttonComponents,
  productUnit,
}: PropTypes) {
  return (
    <Div
      bg="white"
      row
      px={20}
      py={10}
      justifyContent="space-between"
      shadow="md"
    >
      <Div>
        <Text>Total</Text>
        <Text fontSize={14} fontWeight="bold">
          {totalPrice ? formatCurrency(totalPrice) : "TBD"}
        </Text>
        {/* {!!productUnit?.productionCost && (
          <Text fontSize={10}>
            Modal: {formatCurrency(productUnit.productionCost)}
          </Text>
        )} */}
      </Div>
      <Div row>{buttonComponents}</Div>
    </Div>
  )
}
