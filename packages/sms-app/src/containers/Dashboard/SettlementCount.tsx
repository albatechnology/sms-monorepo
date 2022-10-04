import { useNavigation } from "@react-navigation/native"
import React from "react"
import { Pressable, StyleSheet, TouchableOpacity, View } from "react-native"
import { Div, Icon, Text } from "react-native-magnus"
import * as Progress from "react-native-progress"
import { widthPercentageToDP } from "react-native-responsive-screen"

import { responsive } from "helper"

const SettlementCount = ({
  data,
  channelData,
  userData,
  filter,
  startDate,
  endDate,
  totalActivity,
}) => {
  const navigation = useNavigation()
  return (
    <Div row justifyContent="space-between">
      <TouchableOpacity
        onPress={() =>
          navigation.navigate("ReportPipeLineScreen", {
            userData,
            startDate,
            endDate,
            filter,
          })
        }
      >
        <Div
          mb={10}
          p={10}
          bg="white"
          shadow="sm"
          rounded={8}
          minH={90}
          mx={19}
          w={widthPercentageToDP(43)}
          alignItems="flex-start"
        >
          <Text fontSize={12}>Report Brands & Leads</Text>
          <Icon
            p={5}
            fontSize={responsive(16)}
            name="activity"
            color="primary"
            fontFamily="Feather"
          />
        </Div>
      </TouchableOpacity>
      <TouchableOpacity
        onPress={() =>
          navigation.navigate("ActivityTotal", {
            channelData,
            startDate,
            endDate,
            filter,
          })
        }
      >
        <Div
          mb={10}
          p={10}
          bg="white"
          shadow="sm"
          minH={90}
          rounded={8}
          w={widthPercentageToDP(43)}
          mr={19}
        >
          <Div row>
            <Div>
              <Text mb={5} fontSize={12}>
                Activity
              </Text>
              <Text fontWeight="bold" fontSize={16} color="#E53935">
                {totalActivity || 0}
              </Text>
            </Div>
          </Div>
        </Div>
      </TouchableOpacity>
    </Div>
  )
}

export default SettlementCount

const styles = StyleSheet.create({})
