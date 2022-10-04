import { useNavigation } from "@react-navigation/native"
import React from "react"
import { Pressable, StyleSheet, TouchableOpacity, View } from "react-native"
import { Div, Icon, Text } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"

import { responsive } from "helper"

const InteriorDesign = ({
  data,
  startDate,
  endDate,
  filter,
  userData,
  settlement,
}: any) => {
  const navigation = useNavigation()
  return (
    <Div row justifyContent="space-between">
      <TouchableOpacity
        onPress={() =>
          navigation.navigate("InteriorDesignScreen", {
            startDate,
            endDate,
            filter,
            userData,
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
        >
          <Text mb={10} fontSize={12}>
            Interior Design
          </Text>
          <Text fontWeight="bold" fontSize={16} color="#F44336">
            {data?.total}
          </Text>
        </Div>
      </TouchableOpacity>
      {/* New Tabel */}
      <TouchableOpacity
        onPress={() =>
          navigation.navigate("SettlementScreen", {
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
          minH={90}
          rounded={8}
          w={widthPercentageToDP(43)}
          mr={19}
        >
          {!!settlement && settlement.length !== 0 ? (
            settlement.map((e) => (
              <Div row>
                <Div>
                  <Text mb={5} fontSize={12}>
                    Settlement
                  </Text>
                  <Text fontWeight="bold" fontSize={16} color="#E53935">
                    {e.value.value}
                  </Text>
                </Div>
              </Div>
            ))
          ) : (
            <Div row>
              <Div>
                <Text mb={5} fontSize={12}>
                  Settlement
                </Text>
                <Text fontWeight="bold" fontSize={16} color="#E53935">
                  0
                </Text>
              </Div>
            </Div>
          )}
        </Div>
      </TouchableOpacity>
      {/* <TouchableOpacity
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
          minH={90}
          rounded={8}
          w={widthPercentageToDP(43)}
          mr={19}
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
      </TouchableOpacity> */}
    </Div>
  )
}

export default InteriorDesign

const styles = StyleSheet.create({})
