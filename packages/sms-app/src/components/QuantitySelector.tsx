import { LinearGradient } from "expo-linear-gradient"
import React from "react"
import { TouchableOpacity } from "react-native"
import { Button, Div, Icon } from "react-native-magnus"

import ProductCountInput from "components/ProductCountInput"

import { responsive } from "helper"

type PropTypes = {
  disableMinus?: boolean
  disablePlus?: boolean
  onMinus: () => void
  onPlus: () => void
  quantity: number
  onUpdateQuantity: (newQuantity: number) => void
}

export default function QuantitySelector({
  disableMinus = false,
  disablePlus = false,
  onMinus,
  onPlus,
  quantity,
  onUpdateQuantity,
}: PropTypes) {
  return (
    <Div row>
      <TouchableOpacity onPress={onMinus} disabled={disableMinus}>
        <LinearGradient
          style={{
            padding: 5,
            height: responsive(30),
            width: responsive(30),
            justifyContent: "center",
            borderRadius: 4,
          }}
          locations={[0.5, 1.0]}
          colors={["#20B5C0", "#17949D"]}
        >
          <Icon
            name="remove"
            fontSize={14}
            fontFamily="Ionicons"
            color="white"
          />
        </LinearGradient>
      </TouchableOpacity>
      <ProductCountInput quantity={quantity} onChange={onUpdateQuantity} />
      <TouchableOpacity onPress={onPlus} disabled={disablePlus}>
        <LinearGradient
          style={{
            padding: 5,
            height: responsive(30),
            width: responsive(30),
            justifyContent: "center",
            borderRadius: 4,
          }}
          locations={[0.5, 1.0]}
          colors={["#20B5C0", "#17949D"]}
        >
          <Icon name="add" fontSize={14} fontFamily="Ionicons" color="white" />
        </LinearGradient>
      </TouchableOpacity>
    </Div>
  )
}
