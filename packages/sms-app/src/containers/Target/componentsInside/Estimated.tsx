import { useRoute } from "@react-navigation/native"
import moment from "moment"
import React from "react"
import { FlatList } from "react-native"
import { Div, Input, Text, Icon, ScrollDiv } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import useBrandDetail from "api/hooks/target/useBrandDetail"

import { formatCurrency, responsive } from "helper"

const renderItem = ({ item }) => {
  return (
    <Div m={8} rounded={6} bg="#fff" overflow="hidden">
      <Div row justifyContent="space-between">
        <Text
          p={10}
          allowFontScaling={false}
          fontSize={responsive(10)}
          color="#000"
          fontWeight="bold"
        >
          {item?.product_brand}
        </Text>
        <Div
          w={widthPercentageToDP(22)}
          h={heightPercentageToDP(4)}
          alignItems="center"
          roundedBottomLeft={10}
          mb={4}
          justifyContent="center"
          bg={
            item?.brand_category === "PREMIUM"
              ? "#FFD13D"
              : item?.brand_category === "REGULER"
              ? "#0553B7"
              : null
          }
        >
          <Text
            allowFontScaling={false}
            color="#fff"
            fontWeight="bold"
            fontSize={responsive(8)}
          >
            {item?.brand_category}
          </Text>
        </Div>
      </Div>
      <Div
        py={15}
        ml={5}
        row
        borderColor="#c4c4c4"
        borderTopWidth={1}
        justifyContent="space-around"
      >
        <Div>
          <Text allowFontScaling={false} fontSize={responsive(8)}>
            Order Value
          </Text>
          <Text
            allowFontScaling={false}
            color="#17949D"
            fontWeight="bold"
            fontSize={responsive(8)}
          >
            {formatCurrency(item?.order_value)}
          </Text>
        </Div>
        <Div>
          <Text allowFontScaling={false} fontSize={responsive(8)}>
            Pipeline
          </Text>
          <Text
            allowFontScaling={false}
            color="#17949D"
            fontWeight="bold"
            fontSize={responsive(8)}
          >
            {formatCurrency(item?.estimated_value)}
          </Text>
        </Div>
        <Div>
          <Text allowFontScaling={false} fontSize={responsive(8)}>
            Quotation
          </Text>
          <Text
            allowFontScaling={false}
            color="#17949D"
            fontWeight="bold"
            fontSize={responsive(8)}
          >
            {formatCurrency(item?.quotation)}
          </Text>
        </Div>
      </Div>
    </Div>
  )
}

const EstimatedInside = () => {
  const route = useRoute()
  const {
    queries: [{ data: dataList }],
    meta: { isLoading },
  } = useMultipleQueries([
    useBrandDetail({
      id: route?.params?.id,
      company_id: route?.params?.company_id,
      name: route?.params?.name,
      user_type: route?.params?.type,
      start_date: moment(route.params.startDate).format("YYYY-MM-DD"),
      end_date: moment(route.params.endDate).format("YYYY-MM-DD"),
    }),
  ])
  const TopSection = () => {
    return (
      <Div row justifyContent="space-between" bg="primary" p={20}>
        <Div>
          <Text            
            w={widthPercentageToDP(50)}
            allowFontScaling={false}
            fontWeight="bold"
            fontSize={responsive(14)}
            color="white"
          >
            {route.params.company_id === "1"
              ? "Melandas"
              : route.params.company_id === "2"
              ? "Dio Living"
              : route.params.name}
          </Text>
        </Div>
        <Div>
          <Text
            allowFontScaling={false}
            textAlign="right"
            fontSize={responsive(9)}
            color="white"
          >
            Start: {moment(route.params.startDate).format("DD MMMM YYYY")}
          </Text>
          <Text
            allowFontScaling={false}
            textAlign="right"
            fontSize={responsive(9)}
            color="white"
          >
            End: {moment(route.params.endDate).format("DD MMMM YYYY")}
          </Text>
        </Div>
      </Div>
    )
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <ScrollDiv flex={1}>
      <TopSection />
      <FlatList data={dataList?.data} renderItem={renderItem} />
    </ScrollDiv>
  )
}

export default EstimatedInside
