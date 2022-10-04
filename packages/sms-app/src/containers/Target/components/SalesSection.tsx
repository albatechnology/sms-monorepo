import { useNavigation } from "@react-navigation/native"
import React from "react"
import { Image, Pressable, TouchableOpacity } from "react-native"
import { Div, Icon, Text, Tooltip } from "react-native-magnus"
import * as Progress from "react-native-progress"
import { widthPercentageToDP } from "react-native-responsive-screen"

import { responsive } from "helper"

const SalesSection = () => {
  const tooltipRef = React.createRef()
  const navigation = useNavigation()
  const Leads = () => (
    <Pressable onPress={() => navigation.navigate("SalesNewLeads")}>
      <Div
        row
        mt={10}
        bg="white"
        p={15}
        mx={20}
        justifyContent="space-between"
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
        <Div>
          <Div row>
            <Text fontSize={responsive(10)}>New Leads</Text>
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
              text={`Total leads baru yang didaftarkan ke MOVES`}
            />
          </Div>
          <Div row alignItems="center">
            <Text fontSize={responsive(12)} my={5} fontWeight="bold">
              10
            </Text>
            <Icon
              ml={5}
              name="caretup"
              fontFamily="AntDesign"
              fontSize={10}
              color="#2DCC70"
            />
          </Div>
          <Progress.Bar
            borderRadius={0}
            progress={0.6}
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
          source={require("../../../assets/user-plus.png")}
          style={{ width: 40, resizeMode: "contain" }}
        />
      </Div>
    </Pressable>
  )
  return (
    <Div alignItems="center">
      <Leads />
    </Div>
  )
}

export default SalesSection
