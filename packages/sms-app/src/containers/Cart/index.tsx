import { useNavigation } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import { LinearGradient } from "expo-linear-gradient"
import React, { useState } from "react"
import { Pressable, RefreshControl, TouchableOpacity } from "react-native"
import { FlatList } from "react-native-gesture-handler"
import { Button, Checkbox, Div } from "react-native-magnus"
import Modal from "react-native-modal"

import LeadDropdownInput from "components/LeadDropdownInput"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { SelectedType, useCart } from "providers/Cart"

import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { EntryStackParamList } from "Router/EntryStackParamList"

import { formatCurrency } from "helper"

import CartItem from "./CartItem"
import EmptyCart from "./EmptyCart"

type CurrentScreenNavigationProp = StackNavigationProp<
  EntryStackParamList,
  "Checkout"
>

export default () => {
  const [modalVisible, setModalVisible] = useState(false)
  const {
    queries: [{ data: userData }],
    meta: { isError, isLoading, isFetching },
  } = useMultipleQueries([useUserLoggedInData()] as const)
  const {
    cartData,
    filteredCartDataBySelected,
    removeItem,
    addItem,
    resetCart,
    updateItemQuantity,
    reduceItem,
    totalPrice,
    selectedCartData,
    setSelectedCartData,
    toggleSelectedOnId,
    cartIsFetching,
    refetchCart,
  } = useCart()
  const cartSelectedType =
    filteredCartDataBySelected.length === 0
      ? SelectedType.NONE
      : // If the selected cart items length is same as the total, then it's all selected
      filteredCartDataBySelected.length === cartData.length
      ? SelectedType.ALL
      : SelectedType.PARTIAL

  if (cartData.length === 0) {
    return <EmptyCart />
  }
  return (
    <>
      <LeadSelectorModal visible={modalVisible} setVisible={setModalVisible} />
      <Div
        p={20}
        bg="white"
        shadow="sm"
        zIndex={1}
        row
        justifyContent="space-between"
        alignItems="center"
      >
        <Div row alignItems="center">
          <Checkbox
            checked={cartSelectedType === SelectedType.ALL}
            onChange={() => {
              if (
                cartSelectedType === SelectedType.PARTIAL ||
                cartSelectedType === SelectedType.ALL
              ) {
                setSelectedCartData([])
              } else {
                setSelectedCartData(cartData.map((x) => x.productUnitId))
              }
            }}
            // indeterminate={cartSelectedType === SelectedType.PARTIAL}
          />
          <Text>Pilih semua</Text>
        </Div>
        <Pressable
          onPress={() => {
            resetCart()
            setSelectedCartData([])
          }}
        >
          <Text color="grey">Hapus</Text>
        </Pressable>
      </Div>
      <FlatList
        contentContainerStyle={{ flexGrow: 1, backgroundColor: "white" }}
        data={cartData}
        keyExtractor={(item, index) => `cart_item_${index}`}
        showsVerticalScrollIndicator={false}
        ListEmptyComponent={() => (
          <Text fontSize={14} textAlign="center" p={20}>
            Kosong
          </Text>
        )}
        refreshControl={
          <RefreshControl refreshing={cartIsFetching} onRefresh={refetchCart} />
        }
        // onEndReachedThreshold={0.2}
        // onEndReached={() => {
        //   if (hasNextPage) fetchNextPage()
        // }}
        // ListFooterComponent={() =>
        //   !!data &&
        //   data.length > 0 &&
        //   (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        // }
        renderItem={({ item, index }) => (
          <CartItem
            item={item}
            onMinus={() => {
              reduceItem(item.productUnitId)
            }}
            onPlus={() => {
              addItem([
                {
                  productUnitId: item.productUnitId,
                  productUnitData: null,
                },
              ])
            }}
            onUpdateQuantity={(quantity) => {
              updateItemQuantity(item.productUnitId, quantity)
            }}
            onRemove={() => {
              removeItem(item.productUnitId)
            }}
            checked={selectedCartData.includes(item.productUnitId)}
            onCheckChange={() => toggleSelectedOnId(item.productUnitId)}
          />
        )}
      />
      <Div bg="white" row p={20} justifyContent="space-between" shadow="md">
        <Div>
          <Text>Total</Text>
          <Text fontSize={14} fontWeight="bold">
            {formatCurrency(totalPrice)}
          </Text>
        </Div>
        {userData.type === "DIRECTOR" &&
        userData.app_approve_discount === false ? null : (
          <TouchableOpacity onPress={() => setModalVisible(true)}>
            <LinearGradient
              style={{
                paddingVertical: 10,
                paddingHorizontal: 20,
                justifyContent: "center",
                borderRadius: 4,
              }}
              locations={[0.5, 1.0]}
              colors={["#20B5C0", "#17949D"]}
            >
              <Text color="white" fontSize={14}>
                Checkout
              </Text>
            </LinearGradient>
          </TouchableOpacity>
        )}
      </Div>
    </>
  )
}

const LeadSelectorModal = ({ visible = false, setVisible = (val) => {} }) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [leadId, setLeadId] = useState(null)

  return (
    <Modal
      useNativeDriver
      isVisible={visible}
      onBackdropPress={() => setVisible(false)}
    >
      <Div bg="white" p={20}>
        <Text mb={10}>Please select a lead/prospect</Text>
        <LeadDropdownInput value={leadId} onSelect={setLeadId} />
        <Button
          mt={20}
          block
          onPress={() => {
            setVisible(false)
            navigation.navigate("Checkout", { leadId: leadId })
          }}
          bg="primary"
          borderColor="primary"
          borderWidth={0.8}
          alignSelf="center"
          disabled={!leadId}
        >
          Proceed to checkout
        </Button>
      </Div>
    </Modal>
  )
}
