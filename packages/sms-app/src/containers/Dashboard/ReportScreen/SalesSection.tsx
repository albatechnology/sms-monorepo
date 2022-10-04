import { useNavigation } from "@react-navigation/native"
import moment from "moment"
import React from "react"
import { Pressable, StyleSheet, TouchableOpacity, View } from "react-native"
import { Div, Text } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

const SalesSection = ({ data, userData, date, startDate, endDate, filter }) => {
  const navigation = useNavigation()
  const percentage = data[0]?.value.value / data[0]?.target.value
  const routes = () => {
    if (userData.type === "SALES") {
      navigation.navigate("TableRevenue", {
        user: userData,
        filter: {
          startDate: startDate,
          endDate: endDate,
        },
      })
    } else if (userData.supervisorTypeId === 2) {
      navigation.navigate("RevenueStoreLeader", {
        startDate,
        endDate,
        filter,
        userData,
        id: userData?.id,
      })
    } else if (userData.supervisorTypeId === 1) {
      navigation.navigate("RevenueSales", {
        startDate,
        endDate,
        filter,
        userData,
        id: userData?.id,
      })
    } else {
      navigation.navigate("Revenue", {
        date,
        startDate,
        endDate,
        filter,
        userData,
      })
    }
  }

  return (
    <>
      <TouchableOpacity onPress={routes}>
        <Div
          mb={10}
          p={12}
          minH={110}
          bg="white"
          shadow="sm"
          rounded={8}
          mx={19}
          mt={2}
        >
          {data.length !== 0 ? (
            data.map((e) => (
              <Div row>
                <Div>
                  <Text mb={5} fontSize={12}>
                    Sales Revenue
                  </Text>
                  <Text mb={4} fontWeight="bold" fontSize={16} color="#2DCC70">
                    {e.value.format}
                  </Text>
                  <Text fontSize={10} color="#c4c4c4">
                    Target {e.target.format}
                  </Text>
                </Div>
                <Progress.Circle
                  style={{
                    position: "absolute",
                    marginLeft: widthPercentageToDP(65),
                  }}
                  unfilledColor="#F9F9F9"
                  borderWidth={0}
                  size={60}
                  progress={
                    percentage === Infinity || isNaN(percentage)
                      ? 0
                      : percentage
                  }
                  animated={false}
                  thickness={10}
                  showsText={true}
                  color={"green"}
                />
              </Div>
            ))
          ) : (
            <Div row>
              <Div>
                <Text mb={5} fontSize={12}>
                  Sales Revenue
                </Text>
                <Text mb={4} fontWeight="bold" fontSize={16} color="#2DCC70">
                  Rp 0
                </Text>
                <Text fontSize={10} color="#c4c4c4">
                  Target null
                </Text>
              </Div>
              <Progress.Circle
                style={{
                  position: "absolute",
                  marginLeft: widthPercentageToDP(65),
                }}
                unfilledColor="#F9F9F9"
                borderWidth={0}
                size={60}
                progress={0}
                animated={false}
                thickness={10}
                showsText={true}
                color={"green"}
              />
            </Div>
          )}
        </Div>
      </TouchableOpacity>
    </>
  )
}

export default SalesSection

const styles = StyleSheet.create({})
