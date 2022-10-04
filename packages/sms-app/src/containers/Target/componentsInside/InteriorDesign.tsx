import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useState } from "react"
import { FlatList, Pressable } from "react-native"
import { Div, ScrollDiv, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import useMultipleQueries from "hooks/useMultipleQueries"

import useInteriorDesign from "api/hooks/target/useInteriorDesign"

import { formatCurrency, responsive } from "helper"

const InteriorDesignInside = () => {
  const navigation = useNavigation()
  const route = useRoute()

  const {
    queries: [{ data: dataList }],
    meta: { isLoading },
  } = useMultipleQueries([
    useInteriorDesign({
      id: route.params.id,
      user_type: route.params.type,
      start_date: moment(route.params.startDate).format("YYYY-MM-DD"),
      end_date: moment(route.params.endDate).format("YYYY-MM-DD"),
    }),
  ] as const)
  const TopSection = () => {
    return (
      <Div justifyContent="space-between" p={20}>
        <Div>
          <Text
            allowFontScaling={false}
            fontWeight="bold"
            fontSize={responsive(16)}
          >
            {`List of Interior Design (${dataList?.data?.length})`}
          </Text>
        </Div>
      </Div>
    )
  }

  const headerComponent = () => {
    return (
      <Div>
        <Div
          bg="#17949D"
          py={8}
          h={heightPercentageToDP(6)}
          mx={10}
          row
          justifyContent="space-between"
          roundedTopLeft={8}
          roundedTopRight={8}
        >
          <Div flex={1} justifyContent="center" alignItems="center">
            <Text
              allowFontScaling={false}
              fontSize={responsive(10)}
              fontWeight="bold"
              color="#fff"
            >
              No
            </Text>
          </Div>

          <Div flex={3} justifyContent="center" alignItems="center">
            <Text
              allowFontScaling={false}
              w={widthPercentageToDP(40)}
              color="#fff"
              fontSize={responsive(10)}
              fontWeight="bold"
            >
              ID Name
            </Text>
          </Div>
          <Div flex={3} justifyContent="center" alignItems="center">
            <Text
              allowFontScaling={false}
              w={widthPercentageToDP(40)}
              color="#fff"
              fontSize={responsive(10)}
              fontWeight="bold"
            >
              Revenue
            </Text>
          </Div>
        </Div>
      </Div>
    )
  }

  const renderItem = ({ item, index }) => {
    return (
      <Pressable
        onPress={() =>
          navigation.navigate("InteriorDesignDetails", {
            interior_design_id: item?.id,
            name: item?.interior_design,
            start_date: moment(route.params.startDate).format("YYYY-MM-DD"),
            end_date: moment(route.params.endDate).format("YYYY-MM-DD"),
          })
        }
      >
        <Div
          bg="#fff"
          borderBottomColor="#c4c4c4"
          borderBottomWidth={1}
          py={8}
          mx={10}
        >
          <Div row justifyContent="space-between">
            <Div flex={1} justifyContent="center" alignItems="center">
              <Text allowFontScaling={false}>{index + 1}</Text>
            </Div>
            <Div flex={3} justifyContent="center" alignItems="center">
              <Text allowFontScaling={false} w={widthPercentageToDP(40)}>
                {item?.interior_design}
              </Text>
            </Div>
            <Div flex={3} justifyContent="center" alignItems="center">
              <Text allowFontScaling={false} w={widthPercentageToDP(40)}>
                {formatCurrency(item?.revenue)}
              </Text>
            </Div>
          </Div>
        </Div>
      </Pressable>
    )
  }
  return (
    <ScrollDiv flex={1} bg="#f9f7f7">
      <TopSection />
      <FlatList
        data={dataList?.data}
        renderItem={renderItem}
        ListHeaderComponent={headerComponent}
        bounces={false}
      />
    </ScrollDiv>
  )
}

export default InteriorDesignInside
