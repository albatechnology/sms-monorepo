import Case from "case"
import React from "react"
import { Button, Checkbox, Div, Icon } from "react-native-magnus"

import QuantitySelector from "components/QuantitySelector"
import Text from "components/Text"

import { ExtraCartData } from "providers/Cart"

import { formatCurrency } from "helper"

type PropTypes = {
  item: ExtraCartData
  onPlus: () => void
  onMinus: () => void
  onUpdateQuantity?: (quantity: number) => void
  checked: boolean
  onCheckChange: () => void
  onRemove: () => void
}

export default ({
  item,
  onPlus,
  onMinus,
  onUpdateQuantity,
  checked,
  onCheckChange,
  onRemove,
}: PropTypes) => {
  const reduceButtonEnabled = item.quantity > 1

  return (
    <Div flex={1} px={20} pt={20} bg="white" row justifyContent="space-between">
      <Div flex={1} row mr={10}>
        <Checkbox mr={5} checked={checked} onChange={() => onCheckChange()} />
        <Div flex={1}>
          <Text fontSize={14} fontWeight="bold" mb={5}>
            {item?.productUnitData?.name ?? ""}
          </Text>
          <Text color="grey" mb={5}>
            Covering: {Case.title(item?.productUnitData?.covering?.name ?? "-")}
          </Text>
          <Text color="grey" mb={5}>
            Color: {Case.title(item?.productUnitData?.colour?.name ?? "-")}
          </Text>
          <Text mb={10}>{formatCurrency(item?.productUnitData?.price)}</Text>

          <QuantitySelector
            quantity={item.quantity}
            onMinus={onMinus}
            onPlus={onPlus}
            onUpdateQuantity={onUpdateQuantity}
            disableMinus={!reduceButtonEnabled}
          />
        </Div>
      </Div>

      <Button
        bg="primary"
        color="white"
        h={30}
        w={30}
        fontSize={14}
        p={5}
        onPress={onRemove}
      >
        <Icon name="close" fontSize={14} fontFamily="Ionicons" color="white" />
      </Button>
    </Div>
  )
}
