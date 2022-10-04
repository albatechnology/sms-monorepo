import React from "react"
import { useWindowDimensions } from "react-native"
import { Button, Div, Icon } from "react-native-magnus"

import Image from "components/Image"
import Text from "components/Text"

import { responsive } from "helper"
import { COLOR_PRIMARY } from "helper/theme"

export default ({ activeDiscount, setVisible, disabled, discountDetail }) => {
  const { width: screenWidth } = useWindowDimensions()
  return (
    <Div mt={5} px={20} pt={20} bg="white">
      {activeDiscount.length > 0 ? (
        <Button
          block
          py={20}
          px={10}
          borderWidth={1}
          borderColor="grey"
          bg="white"
          justifyContent="flex-start"
          onPress={() => setVisible(true)}
          disabled={disabled}
        >
          <Div flex={1} row justifyContent="space-between" alignItems="center">
            <Div row alignItems="center">
              <Image
                width={responsive(24)}
                scalable
                source={require("assets/icon_promo.png")}
                style={{ tintColor: COLOR_PRIMARY }}
              />
              <Div ml={10} maxW={0.6 * screenWidth}>
                {discountDetail.map((e) => (
                  <Text fontSize={14} fontWeight="bold">
                    {e?.name}
                  </Text>
                ))}

                <Text>{activeDiscount?.description}</Text>
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
      ) : (
        <Button
          block
          py={20}
          px={10}
          borderWidth={1}
          borderColor="grey"
          bg="white"
          justifyContent="flex-start"
          onPress={() => setVisible(true)}
          disabled={disabled}
        >
          <Div flex={1} row justifyContent="space-between" alignItems="center">
            <Div row alignItems="center">
              <Image
                width={responsive(24)}
                scalable
                source={require("assets/icon_promo.png")}
                style={{ tintColor: COLOR_PRIMARY }}
              />
              <Text ml={10} fontSize={14} fontWeight="bold">
                Discount
              </Text>
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
      )}
    </Div>
  )
}
