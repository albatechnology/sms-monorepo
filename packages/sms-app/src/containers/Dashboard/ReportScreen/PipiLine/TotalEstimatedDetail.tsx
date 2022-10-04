import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React from "react"
import { FlatList, Pressable, ScrollView } from "react-native"
import { Button, Div, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import { formatCurrency } from "helper"

const TotalEstimatedDetail = () => {
  const route = useRoute()
  const data = route.params
  const navigation = useNavigation()
  const isLoading = null

  console.log(data, "Check Route Estimated Detail")

  const renderItem = ({ item }) => (
    <Pressable onPress={undefined}>
      <Div py={14} row bg="white" borderBottomWidth={0.5} borderColor="grey">
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {item.product_brand}
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text fontSize={10} textAlign="center">
            {formatCurrency(item.estimated_value)}
          </Text>
        </Div>
      </Div>
    </Pressable>
  )

  if (isLoading) {
    return <Loading />
  }
  return (
    <ScrollView style={{ flex: 1, backgroundColor: "#fff" }}>
      <Div p={15}>
        <Div row justifyContent="space-between" p={5}>
          <Text>Sales :</Text>
          <Text>{data[0].sales}</Text>
        </Div>
        <Div row justifyContent="space-between" p={5}>
          <Text>Customer :</Text>
          <Text>{data[0].customer}</Text>
        </Div>
        <Div row justifyContent="space-between" p={5}>
          <Text>Date :</Text>
          <Text>{moment(data[0].created_at).format("YYYY-MM-DD")}</Text>
        </Div>
        <Div row justifyContent="space-between" p={5}>
          <Button
            h="auto"
            bg="#20B5C0"
            fontSize={12}
            onPress={() =>
              navigation.navigate("ActivityDetail", {
                id: data[0]?.activity_id,
                isDeals: true,
              })
            }
          >
            Go to activity
          </Button>
        </Div>
      </Div>
      <Div
        py={18}
        row
        bg="#20B5C0"
        justifyContent="center"
        style={{
          height: heightPercentageToDP(9),
          width: widthPercentageToDP(100),
        }}
      >
        <Div flex={3} justifyContent="center">
          <Text
            color="white"
            fontWeight="bold"
            textAlign="center"
            fontSize={10}
            allowFontScaling={false}
          >
            Brand
          </Text>
        </Div>
        <Div flex={3} justifyContent="center">
          <Text
            color="white"
            fontWeight="bold"
            textAlign="center"
            fontSize={10}
            allowFontScaling={false}
          >
            Estimated
          </Text>
        </Div>
      </Div>
      {data.map((item) => (
        <Div
          py={14}
          bg="#fff"
          row
          borderBottomWidth={1}
          borderColor="#c4c4c4"
          rounded={0}
          h={heightPercentageToDP(9)}
          w={widthPercentageToDP(100)}
          justifyContent="center"
        >
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              fontSize={8}
              textAlign="center"
              allowFontScaling={false}
            >
              {item?.brand}
            </Text>
          </Div>
          <Div flex={3} justifyContent="center">
            <Text
              fontWeight="normal"
              fontSize={8}
              textAlign="center"
              allowFontScaling={false}
            >
              {formatCurrency(item?.estimated_value)}
            </Text>
          </Div>
        </Div>
      ))}
    </ScrollView>
  )
}

export default TotalEstimatedDetail
