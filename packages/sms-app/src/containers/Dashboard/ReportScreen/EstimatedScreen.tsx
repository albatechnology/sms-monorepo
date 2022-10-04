import React, { useState } from "react"
import { Dimensions, FlatList } from "react-native"
import { ScrollView } from "react-native-gesture-handler"
import { Button, Div, Icon, ScrollDiv, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import { useAxios } from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { useAuth } from "providers/Auth"

const EstimatedScreen = () => {
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [data, setData] = useState([])
  const windowWidth = Dimensions.get("window").width

  const ReportBrands = useQuery<string, any>(["ReportBrands", loggedIn], () => {
    return axios
      .get(`dashboard/report-brands`)
      .then((res) => {
        console.log(res)
        setData(res.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })

  const renderEstimated = ({ item }) => (
    <>
      <Div>
        <Div
          py={18}
          row
          bg="red"
          w={widthPercentageToDP(40)}
          // style={{  borderTopRightRadius: 10, borderTopLeftRadius: 10 }}
        >
          <Div flex={3}>
            <Text color="white" fontWeight="bold" textAlign="center">
              {item?.product_brand}
            </Text>
          </Div>
        </Div>
        <Div
          py={14}
          bg="white"
          row
          borderBottomWidth={1}
          borderColor="#c4c4c4"
          rounded={0}
        >
          <Div flex={3} h={heightPercentageToDP(2.95)}>
            <Text fontWeight="normal" textAlign="center">
              {item?.estimated_value}
            </Text>
          </Div>
        </Div>
      </Div>
    </>
  )

  const singleHeader = (title: string, title1: string, title2: string) => {
    return (
      <Div py={18} row bg="#17949D" w={widthPercentageToDP(30)}>
        <Div flex={2}>
          <Text color="white" fontWeight="bold" textAlign="center">
            Name
          </Text>
        </Div>
      </Div>
    )
  }
  const renderSingle = ({ item }) => (
    <>
      <Div
        py={18}
        bg="white"
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
      >
        <Div flex={3} h={heightPercentageToDP(2)}>
          <Text
            fontWeight="normal"
            textAlign="center"
            w={widthPercentageToDP(30)}
          >
            {item.sales}
          </Text>
        </Div>
      </Div>
    </>
  )

  const totalHeader = (title: string, title1: string, title2: string) => {
    return (
      <Div py={18} row bg="red" w={widthPercentageToDP(30)}>
        <Div flex={2}>
          <Text color="white" fontWeight="bold" textAlign="center">
            Total Estimated
          </Text>
        </Div>
      </Div>
    )
  }
  const renderTotal = ({ item }) => (
    <>
      <Div
        py={18}
        bg="white"
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
      >
        <Div flex={3} h={heightPercentageToDP(2)}>
          <Text
            fontWeight="normal"
            textAlign="center"
            w={widthPercentageToDP(30)}
          >
            {item.total_estimated}
          </Text>
        </Div>
      </Div>
    </>
  )
  return (
    <ScrollView style={{ flex: 1 }}>
      <Div row flex={1}>
        <FlatList
          style={{ width: widthPercentageToDP(40) }}
          data={data}
          renderItem={renderSingle}
          keyExtractor={(_, idx: number) => idx.toString()}
          ListHeaderComponent={singleHeader}
        />
        <ScrollView
          style={{ width: windowWidth }}
          horizontal
          showsHorizontalScrollIndicator={false}
          scrollEventThrottle={16}
          bounces={false}
        >
          <FlatList
            horizontal
            // style={{ width: windowWidth}}
            data={data[0]?.product_brands}
            renderItem={renderEstimated}
          />

          <FlatList
            data={data}
            renderItem={renderTotal}
            ListHeaderComponent={totalHeader}
          />
        </ScrollView>
      </Div>
    </ScrollView>
  )
}

export default EstimatedScreen
