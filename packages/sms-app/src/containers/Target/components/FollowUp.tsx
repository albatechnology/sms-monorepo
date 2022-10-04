import React from "react"
import { View, Image, FlatList, TouchableOpacity } from "react-native"
import { Div, Icon, Text, Tooltip } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import { responsive } from "helper"

const FollowUp = () => {
  const status = [
    {
      status: "Hot",
      total: 12,
    },
    {
      status: "Warm",
      total: 19,
    },
    {
      status: "Cold",
      total: 126,
    },
    {
      status: "Closed",
      total: 12,
    },
  ]
  const tooltipRef = React.createRef()
  const renderItem = ({ item }) => (
    <Div
      alignItems="center"
      row
      h={heightPercentageToDP(5)}
      justifyContent="space-between"
      borderTopWidth={1}
      borderColor="#D9D9D9"
    >
      <Div row justifyContent="center" alignItems="center">
        <Div mx={8} h={8} w={8} rounded={8 / 2} bg="red" />
        <Text>{item.status}</Text>
      </Div>
      <Text>{item.total}</Text>
    </Div>
  )
  const Leads = () => (
    <Div
      mt={10}
      bg="white"
      p={15}
      mx={20}
      w={widthPercentageToDP(90)}
      rounded={8}
      style={{
        shadowColor: "#000",
        shadowOffset: {
          width: 0,
          height: 2,
        },
        shadowOpacity: 0.23,
        shadowRadius: 2.62,
        elevation: 4,
      }}
    >
      <Div row justifyContent="space-between">
        <Div>
          <Div row>
            <Text fontSize={responsive(10)}>Follow Up</Text>
            <TouchableOpacity
              onPress={() => {
                if (tooltipRef.current) {
                  tooltipRef.current.show()
                }
              }}
            >
              <Icon
                ml={5}
                name="info"
                color="grey"
                fontFamily="Feather"
                fontSize={12}
              />
            </TouchableOpacity>
            <Tooltip
              ref={tooltipRef}
              mr={widthPercentageToDP(10)}
              text={`Jumlah Follow up yang dilakukan ke customer`}
            />
          </Div>
          <Div row alignItems="center">
            <Text fontSize={responsive(12)} my={5} fontWeight="bold">
              10
            </Text>
            <Icon
              ml={5}
              name="caretdown"
              fontFamily="AntDesign"
              fontSize={10}
              color="#F44336"
            />
          </Div>
          <Progress.Bar
            borderRadius={0}
            progress={0.4}
            color="#17949D"
            borderWidth={0}
            height={5}
            useNativeDriver
            unfilledColor="#c4c4c4"
            width={widthPercentageToDP("60%")}
          />
          <Text mt={5} fontSize={responsive(8)} color="#c4c4c4">
            Target 15
          </Text>
        </Div>
        <Image
          source={require("../../../assets/follow-target.png")}
          style={{ width: 40, resizeMode: "contain" }}
        />
      </Div>
      <FlatList data={status} renderItem={renderItem} />
    </Div>
  )
  return (
    <Div alignItems="center">
      <Leads />
    </Div>
  )
}

export default FollowUp
