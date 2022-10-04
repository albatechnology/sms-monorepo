import Case from "case"
import React, { useState } from "react"
import { Pressable, Image } from "react-native"
import { Button, Checkbox, Div, Icon, Modal } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import LocationDropdownList from "components/LocationDropdownList"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { ExtraCartData, useCart } from "providers/Cart"

import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { formatCurrency, responsive } from "helper"

type PropTypes = {
  item: ExtraCartData
  onRemove: (id) => void
  index: number
}
export default ({ item, onRemove, index }: PropTypes) => {
  const { isReadyStock, setLocation } = useCart()
  const [status, setStatus] = useState("ready")
  const [selectedLocation, setSelectedLocation] = useState()
  const [modalVisible, setModalVisible] = useState(false)
  const [visible, setVisible] = useState(false)
  const {
    queries: [{ data: userData }],
    meta: { isError, isFetching, refetch },
  } = useMultipleQueries([useUserLoggedInData()] as const)
  const hideModal = () => {
    setModalVisible(false)
  }
  return (
    <Div flex={1} px={20} pb={20} bg="white" row justifyContent="space-between">
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
        <Text color="grey" mb={5}>
          Status:{" "}
          {Case.title(item?.is_ready === false ? "Indent" : "Ready stock")}
        </Text>
        <Text color="grey" mb={5}>
          Location: {Case.title(item?.location?.name ?? "-")}
        </Text>
        <Text mb={5}>{formatCurrency(item?.productUnitData?.price)}</Text>
        <Text mb={10}>Qty: {item.quantity}</Text>
      </Div>
      <Div position="absolute" ml={widthPercentageToDP(80)} zIndex={999}>
        <Button
          bg="white"
          color="primary"
          borderWidth={1}
          w={widthPercentageToDP(18)}
          fontSize={10}
          borderColor="#c4c4c4"
          onPress={() => setModalVisible(!modalVisible)}
        >
          Option +
        </Button>
      </Div>
      <Div justifyContent="flex-end">
        <Pressable onPress={() => onRemove(item.productUnitId)}>
          <Icon
            p={10}
            name="trash"
            color="primary"
            fontSize={16}
            fontFamily="Ionicons"
          />
        </Pressable>
      </Div>
      <Modal
        useNativeDriver
        isVisible={modalVisible}
        onBackdropPress={hideModal}
        animationIn={"slideInUp"}
        onBackButtonPress={hideModal}
        onDismiss={hideModal}
        onModalHide={hideModal}
        h="60%"
      >
        <>
          <Div shadow="sm" p={20} bg="white">
            <Text fontSize={16} fontWeight="bold">
              Select stock
            </Text>
          </Div>
          <Div row p={15} alignItems="center">
            <Text fontSize={12}>Status : </Text>
            <Button
              mx={10}
              onPress={() => {
                setStatus("ready")
                isReadyStock(item.productUnitId, true)
              }}
              bg="white"
              borderColor={status === "indent" ? "grey" : "#17949D"}
              color="black"
              borderWidth={2}
            >
              Ready
            </Button>
            <Button
              bg="white"
              onPress={() => {
                setStatus("indent")
                isReadyStock(item.productUnitId, false)
              }}
              borderColor={status === "ready" ? "grey" : "#17949D"}
              color="black"
              borderWidth={2}
            >
              Indent
            </Button>
          </Div>
          {status === "indent" ? (
            <Div rounded={4} bg="#ecf0f1" p={20}>
              <Text>
                If status indent, the stock will be automatically taken from:
              </Text>
              <Text>- Melandas 100WH</Text>
              <Text>- Dio Living 302N</Text>
            </Div>
          ) : (
            <LocationDropdownList
              value={selectedLocation}
              title="Select warehouse location"
              sku={item.productUnitData.sku}
              message="Select Location"
              companyId={userData?.companyId}
              onSelect={(val) => {
                setSelectedLocation(val)
                setLocation(item.productUnitId, val)
              }}
            />
          )}
        </>
      </Modal>
    </Div>
  )
}
