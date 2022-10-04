import React, { useState } from "react"
import { Dispatch } from "react"
import CurrencyInput from "react-native-currency-input"
import { Button, Checkbox, Div, Modal } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Text from "components/Text"

import "helper"
import s, { COLOR_DISABLED } from "helper/theme"

type PropTypes = {
  visible: boolean
  setVisible: Dispatch<boolean>
  packingFee: number
  setPackingFee: Dispatch<number>
  shippingFee: number
  setShippingFee: Dispatch<number>
  additionalDiscount: number
  setAdditionalDiscount: Dispatch<number>
  setDiscountType: Dispatch<number>
}

export default ({
  visible,
  setVisible,
  packingFee,
  shippingFee,
  additionalDiscount,
  setPackingFee,
  setShippingFee,
  setAdditionalDiscount,
  setDiscountType,
}: PropTypes) => {
  const [packing, setPacking] = useState<number>(packingFee)
  const [shipping, setShipping] = useState<number>(shippingFee)
  const [additionalDiscountLocal, setAdditionalDiscountLocal] =
    useState<number>(additionalDiscount)
  const [toggle, setToggle] = useState(0)
  const borderStyle = {
    borderWidth: 1,
    borderColor: COLOR_DISABLED,
  }

  const hideModal = () => {
    setVisible(false)
    setToggle(0)
  }

  const handleSubmit = async () => {
    await setPackingFee(packing)
    await setShippingFee(shipping)
    await setAdditionalDiscount(additionalDiscountLocal)
    await setDiscountType(toggle)
    hideModal()
  }
  return (
    <Modal
      useNativeDriver
      isVisible={visible}
      onBackdropPress={hideModal}
      animationIn={"slideInUp"}
      onBackButtonPress={hideModal}
      h="80%"
    >
      <>
        <Div shadow="sm" p={20} bg="white">
          <Text fontSize={16} fontWeight="bold">
            Additional Fee
          </Text>
        </Div>

        <Div p={20}>
          <Div mb={20}>
            <Text fontSize={14} fontWeight="bold" mb={10}>
              Packing Fee:
            </Text>
            <CurrencyInput
              value={packing}
              returnKeyType={"done"}
              onChangeValue={(val) => (!!val ? setPacking(val) : setPacking(0))}
              prefix="Rp."
              delimiter="."
              separator=","
              precision={0}
              style={[s.bgWhite, s.p10, borderStyle]}
            />
          </Div>

          <Div mb={20}>
            <Text fontSize={14} fontWeight="bold" mb={10}>
              Shipping Fee:
            </Text>
            <CurrencyInput
              value={shipping}
              returnKeyType={"done"}
              onChangeValue={(val) =>
                !!val ? setShipping(val) : setShipping(0)
              }
              prefix="Rp."
              delimiter="."
              separator=","
              precision={0}
              style={[s.bgWhite, s.p10, borderStyle]}
            />
          </Div>

          <Div mb={20}>
            <Text fontSize={14} fontWeight="bold" mb={10}>
              Additional Discount:
            </Text>
            <Checkbox
              value={toggle === 0 ? 1 : 0}
              defaultChecked={false}
              onChange={(value) => {
                setToggle(value)
                setShipping(0)
              }}
              suffix={<Text>Discount in Percent</Text>}
              my={heightPercentageToDP(1)}
            />
            {toggle === 0 ? (
              <CurrencyInput
                value={additionalDiscountLocal}
                returnKeyType={"done"}
                onChangeValue={(val) =>
                  !!val
                    ? setAdditionalDiscountLocal(val)
                    : setAdditionalDiscountLocal(0)
                }
                prefix="Rp."
                delimiter="."
                separator=","
                precision={0}
                style={[s.bgWhite, s.p10, borderStyle]}
              />
            ) : (
              <CurrencyInput
                value={additionalDiscountLocal}
                returnKeyType={"done"}
                onChangeValue={(val) =>
                  !!val
                    ? setAdditionalDiscountLocal(val)
                    : setAdditionalDiscountLocal(0)
                }
                suffix="%"
                maxLength={3}
                precision={0}
                style={[s.bgWhite, s.p10, borderStyle]}
              />
            )}
          </Div>

          <Button
            onPress={handleSubmit}
            bg="primary"
            mt={30}
            px={20}
            alignSelf="center"
            w={"100%"}
          >
            <Text fontWeight="bold" color="white">
              Apply
            </Text>
          </Button>
        </Div>
      </>
    </Modal>
  )
}
