import { useNavigation } from "@react-navigation/native"
import React, { useState } from "react"
import { FlatList } from "react-native"
import { Button, Div, Text } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { formatCurrency } from "helper"

const NewProduct = ({ data, setVisible, setIndex, onRemove }) => {
  const [id, setId] = useState(null)
  const { loggedIn } = useAuth()
  const axios = useAxios()
  const handleSubmit = () => {
    axios
      .delete(
        `cart-demands/${id}`,

        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        // console.log(res, "succes delete")
      })
      .catch((err) => {
        console.log(err)
      })
      .finally(() => {
        onRemove()
      })
  }
  const renderItem = ({ item, index }) => (
    <Div
      bg="white"
      mx={20}
      mb={10}
      row
      borderBottomWidth={0.5}
      borderColor="#c4c4c4"
      justifyContent="space-between"
    >
      <Div>
        <Text fontWeight="bold" fontSize={17} w={widthPercentageToDP(70)}>
          {item?.name}
        </Text>
        <Text mt={5}>{formatCurrency(item?.price)}</Text>
        <Text mt={5} mb={10}>
          Qty: {item?.quantity}
        </Text>
      </Div>
      <Button
        p={5}
        bg={"#EB5757"}
        mb={10}
        alignSelf="flex-end"
        onPress={async () => {
          await setId(item.id)
          handleSubmit()
        }}
      >
        Delete
      </Button>
    </Div>
  )
  const footer = () => (
    <Button my={20} ml={20} bg="#c4c4c4" onPress={() => setVisible(true)}>
      <Text color="white">Tambah product baru</Text>
    </Button>
  )
  return (
    <>
      <Div
        mt={5}
        p={20}
        bg="white"
        row
        w={"100%"}
        justifyContent="space-between"
      >
        <Text fontSize={14} fontWeight="bold">
          Add Product
        </Text>
      </Div>
      <Div bg="white">
        <FlatList
          renderItem={renderItem}
          data={data}
          ListFooterComponent={footer}
        />
      </Div>
    </>
  )
}

export default NewProduct
