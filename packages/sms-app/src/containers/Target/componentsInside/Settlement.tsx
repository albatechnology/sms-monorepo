import React from "react"
import { render } from "react-dom"
import { FlatList } from "react-native"
import { Div, Icon, Input, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"

import { responsive } from "helper"

const SettlementInside = () => {
  const data = [
    {
      no: 1,
      name: "Michael",
      inv: "INV123455",
      date: "12-09-2022",
    },
    {
      no: 2,
      name: "William",
      inv: "INV123455",
      date: "12-09-2022",
    },
    {
      no: 3,
      name: "Tobby",
      inv: "INV123455",
      date: "13-09-2022",
    },
  ]
  const TopSection = () => {
    return (
      <Div justifyContent="space-between" p={20}>
        <Div>
          <Text
            allowFontScaling={false}
            fontWeight="bold"
            fontSize={responsive(16)}
          >
            Settlement (10)
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
          mx={10}
          row
          justifyContent="space-between"
          roundedTopLeft={8}
          roundedTopRight={8}
        >
          <Div flex={2} justifyContent="center" alignItems="center">
            <Text
              allowFontScaling={false}
              fontSize={responsive(10)}
              fontWeight="bold"
              color="#fff"
            >
              Date
            </Text>
          </Div>

          <Div flex={3} justifyContent="center" alignItems="center">
            <Text
              allowFontScaling={false}
              color="#fff"
              fontSize={responsive(10)}
              fontWeight="bold"
            >
              Invoice Number
            </Text>
          </Div>
          <Div flex={3} justifyContent="center" alignItems="center">
            <Text
              allowFontScaling={false}
              color="#fff"
              fontSize={responsive(10)}
              fontWeight="bold"
            >
              Customer Name
            </Text>
          </Div>
        </Div>
      </Div>
    )
  }
  const renderItem = ({ item }) => {
    return (
      <Div
        bg="#fff"
        borderBottomColor="#c4c4c4"
        borderBottomWidth={1}
        py={8}
        mx={10}
      >
        <Div row justifyContent="space-between">
          <Div flex={2} justifyContent="center" alignItems="center">
            <Text allowFontScaling={false}>{item?.date}</Text>
          </Div>
          <Div flex={3} justifyContent="center" alignItems="center">
            <Text allowFontScaling={false}>{item?.inv}</Text>
          </Div>
          <Div flex={3} justifyContent="center" alignItems="center">
            <Text allowFontScaling={false}>{item?.name}</Text>
          </Div>
        </Div>
      </Div>
    )
  }

  return (
    <Div>
      <TopSection />
      <Input
        placeholder="Search Invoice/Customer"
        ml={heightPercentageToDP(2)}
        mr={heightPercentageToDP(2)}
        mb={heightPercentageToDP(5)}
        focusBorderColor="#17949D"
        prefix={<Icon name="search" color="gray900" fontFamily="Feather" />}
      />
      <FlatList
        data={data}
        renderItem={renderItem}
        ListHeaderComponent={headerComponent}
      />
    </Div>
  )
}

export default SettlementInside
