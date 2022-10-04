import Case from "case"
import React from "react"
import { Div } from "react-native-magnus"

import Text from "components/Text"

import { formatCurrency } from "helper"

import { Order } from "types/Order"

type PropTypes = {
  item: Order["orderDetails"]
}

export default ({ item }: PropTypes) => {
  return (
    <Div flex={1} px={20} pb={20} bg="white" row justifyContent="space-between">
      <Div flex={1}>
        <Text fontSize={14} fontWeight="bold" mb={5}>
          {item?.productUnit?.name ?? ""}
        </Text>
        <Text color="grey" mb={5}>
          Covering: {Case.title(item?.covering?.name ?? "-")}
        </Text>
        <Text color="grey" mb={5}>
          Color: {Case.title(item?.colour?.name ?? "-")}
        </Text>
        <Text mb={5}>{formatCurrency(item?.productUnit?.price)}</Text>
        <Text mb={10}>Qty: {item.quantity}</Text>
      </Div>
    </Div>
  )
}
