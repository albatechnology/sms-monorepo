import { NavigationContainer, useNavigation } from "@react-navigation/native"
import React, { useEffect, useState } from "react"
import { FlatList, Pressable, StyleSheet } from "react-native"
import { Div, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

const BrandCategory = ({ data, userData, startDate, endDate }) => {
  const navigation = useNavigation()
  const colors = [
    "#E53935",
    "#FFD13D",
    "#0553B7",
    "#007A33",
    "#EB9CA8",
    "#7C878E",
  ]
  const renderBrand = ({ item, index }) => (
    <Pressable
      onPress={() =>
        navigation.navigate("BrandDetail", {
          id: item.id,
          userData,
          startDate,
          endDate,
        })
      }
    >
      <Div
        bg="white"
        mt={10}
        borderWidth={1}
        mx={5}
        alignSelf="center"
        justifyContent="center"
        style={{
          borderRadius: 8,
          height: heightPercentageToDP(8),
          borderColor: colors[index % colors.length],
          width: widthPercentageToDP(40),
        }}
      >
        <Text color="#c4c4c4" ml={10}>
          {item.name}
        </Text>
        <Text fontWeight="bold" fontSize={14} ml={10}>
          {item.total_price}
        </Text>
      </Div>
    </Pressable>
  )
  return (
    <Div mb={10} p={10} bg="white" shadow="sm" rounded={8} mx={19}>
      <Text>Brand Category</Text>
      <Div style={{ width: widthPercentageToDP(86) }} alignItems="center">
        <FlatList
          renderItem={renderBrand}
          data={data}
          keyExtractor={(_, idx: number) => idx.toString()}
          numColumns={2}
        />
      </Div>
    </Div>
  )
}

export default BrandCategory

const styles = StyleSheet.create({})
