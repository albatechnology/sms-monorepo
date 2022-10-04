import React, { useState } from "react"
import { Dispatch } from "react"
import CurrencyInput from "react-native-currency-input"
import { Button, Div, Input, Modal } from "react-native-magnus"

import Text from "components/Text"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import "helper"
import s, { COLOR_DISABLED } from "helper/theme"

type PropTypes = {
  visible: boolean
  setVisible?: Dispatch<boolean>
  packingFee?: number
  setPackingFee?: Dispatch<number>
  shippingFee?: number
  setShippingFee?: Dispatch<number>
  additionalDiscount?: number
  setAdditionalDiscount?: Dispatch<number>
  productName: string
  setProductName: Dispatch<string>
  productQty: number
  setProductQty: Dispatch<number>
  onClick: any
}

export default ({
  visible,
  setVisible,
  packingFee,
  shippingFee,
  additionalDiscount,
  productName,
  setProductName,
  productQty,
  setProductQty,
  setPackingFee,
  setShippingFee,
  setAdditionalDiscount,
  onClick,
}: PropTypes) => {
  const [name, setName] = useState<string>(productName)
  const [qty, setQty] = useState<number>(productQty)
  const [additionalDiscountLocal, setAdditionalDiscountLocal] =
    useState<number>(additionalDiscount)
  const borderStyle = {
    borderWidth: 1,
    borderColor: COLOR_DISABLED,
  }
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const hideModal = () => {
    setVisible(false)
  }

  const handlePress = () => {
    axios
      .post(
        `cart-demands`,
        {
          items: [
            {
              name: name,
              price: additionalDiscountLocal,
              quantity: qty,
            },
          ],
        },
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        console.log(res, "succes submit")
      })
      .catch((err) => {
        console.log(err)
      })
      .finally(() => {
        onClick()
      })
  }
  const handleSubmit = () => {
    setProductName(name)
    setProductQty(qty)
    setAdditionalDiscount(additionalDiscountLocal)
    hideModal()
    handlePress()
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
            Add New Product
          </Text>
        </Div>

        <Div p={20}>
          <Div mb={20}>
            <Text fontSize={14} fontWeight="bold" mb={10}>
              Product Name
            </Text>
            <Input
              value={name}
              onChangeText={setName}
              placeholder="Masukan Nama Barang"
            />
          </Div>

          <Div mb={20}>
            <Text fontSize={14} fontWeight="bold" mb={10}>
              Qty
            </Text>
            <Input
              value={qty}
              onChangeText={setQty}
              returnKeyType={"done"}
              keyboardType="numeric"
              placeholder="Masukan Jumlah Barang"
            />
          </Div>

          <Div mb={20}>
            <Text fontSize={14} fontWeight="bold" mb={10}>
              Price
            </Text>
            <CurrencyInput
              returnKeyType={"done"}
              value={additionalDiscountLocal}
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
