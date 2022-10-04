import React from "react"
import { useWindowDimensions } from "react-native"
import { Button, Div, Icon } from "react-native-magnus"

import Image from "components/Image"
import Text from "components/Text"

import { formatCurrency, responsive } from "helper"
import { COLOR_PRIMARY } from "helper/theme"

export default ({
  setVisible,
  packingFee,
  shippingFee,
  additionalDiscount,
}) => {
  const { width: screenWidth } = useWindowDimensions()

  return (
    <Div px={20} pt={10} pb={20} bg="white">
      <Button
        block
        py={20}
        px={10}
        borderWidth={1}
        borderColor="grey"
        bg="white"
        justifyContent="flex-start"
        onPress={() => setVisible(true)}
      >
        <Div flex={1} row justifyContent="space-between" alignItems="center">
          <Div row alignItems="center">
            <Image
              width={responsive(24)}
              scalable
              source={require("assets/icon_additional_fee.png")}
              style={{ tintColor: COLOR_PRIMARY }}
            />
            <Div ml={10} maxW={0.6 * screenWidth}>
              <Text fontSize={14} fontWeight="bold">
                Price adjustments
              </Text>
              {!!packingFee && (
                <Text mt={5}>Packing fee {formatCurrency(packingFee)}</Text>
              )}
              {!!shippingFee && (
                <Text my={5}>Shipping fee {formatCurrency(shippingFee)}</Text>
              )}
              {!!additionalDiscount && (
                <Text my={5}>
                  Additional Discount {formatCurrency(additionalDiscount)}
                </Text>
              )}
            </Div>
          </Div>
          <Icon
            p={5}
            name="chevron-forward"
            color="primary"
            fontSize={18}
            fontFamily="Ionicons"
          />
        </Div>
      </Button>
    </Div>
  )
}
