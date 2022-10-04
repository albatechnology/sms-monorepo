import { useRoute } from "@react-navigation/native"
import moment from "moment"
import React from "react"
import { FlatList } from "react-native"
import { Button, Div, Dropdown, Icon, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"

import useMultipleQueries from "hooks/useMultipleQueries"

import useInteriorDesignDetails from "api/hooks/target/useInteriorDesignDetails"

import { formatCurrency, responsive } from "helper"

const InteriorDesignDetails = () => {
  const route = useRoute()
  const {
    queries: [{ data: dataList }],
    meta: { isLoading },
  } = useMultipleQueries([
    useInteriorDesignDetails({
      interior_design_id: route.params.interior_design_id,
      start_date: route.params.start_date,
      end_date: route.params.end_date,
    }),
  ])
  console.log(route.params, "Check interior design dataList")
  const TopSection = () => {
    return (
      <Div row justifyContent="space-between" p={20} bg="primary">
        <Div justifyContent="center">
          <Text
            allowFontScaling={false}
            color="white"
            fontWeight="bold"
            fontSize={responsive(16)}
          >
            {route?.params?.name}
          </Text>
        </Div>
      </Div>
    )
  }

  const renderItem = ({ item }) => {
    return (
      <Div m={5} mx={10} rounded={8} bg="#fff" p={10}>
        <Div p={5}>
          <Text
            allowFontScaling={false}
            fontSize={responsive(9)}
            color="#c4c4c4"
          >
            {item?.invoice_number}
          </Text>
          <Text
            allowFontScaling={false}
            fontSize={responsive(9)}
            color="#c4c4c4"
          >
            {moment(item?.created_at).format("DD-MM-YYYY")}
          </Text>
        </Div>
        <Div ml={5}>
          <Text
            allowFontScaling={false}
            fontWeight="bold"
            fontSize={responsive(10)}
          >
            {formatCurrency(item?.total_price)}
          </Text>
          <Text allowFontScaling={false} fontSize={responsive(10)}>
            {item?.channel} - {item?.sales}
          </Text>
        </Div>
      </Div>
    )
  }

  return (
    <Div>
      <TopSection />
      <FlatList renderItem={renderItem} data={dataList?.data} />
    </Div>
  )
}

export default InteriorDesignDetails
