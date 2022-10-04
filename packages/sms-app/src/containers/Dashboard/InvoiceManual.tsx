import { useNavigation } from "@react-navigation/native"
import React from "react"
import { Pressable, StyleSheet, TouchableOpacity, View } from "react-native"
import { Div, Text } from "react-native-magnus"

const InvoiceManual = ({ data, startDate, endDate, filter, userData }) => {
  const navigation = useNavigation()
  return (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("InvoiceScreen", {
          startDate,
          endDate,
          filter,
          userData,
        })
      }
    >
      <Div mb={10} p={10} bg="white" shadow="sm" rounded={8} minH={90} mx={19}>
        <Text mb={10} fontSize={12}>
          Invoice Manual
        </Text>
        <Text fontWeight="bold" fontSize={16} color="#0553B7">
          {data}
        </Text>
      </Div>
    </TouchableOpacity>
  )
}

export default InvoiceManual

const styles = StyleSheet.create({})
