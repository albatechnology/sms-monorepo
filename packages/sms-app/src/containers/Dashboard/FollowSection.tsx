import { useNavigation } from "@react-navigation/native"
import React from "react"
import { FlatList, Pressable, View } from "react-native"
import { Dimensions } from "react-native"
import { PieChart } from "react-native-chart-kit"
import { Div, Text } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

const FollowSection = ({ data }) => {
  const navigation = useNavigation()
  var percentage = data.reduce((n, { value }) => n + value.value, 0)
  const chartConfig = {
    backgroundGradientFrom: "#1E2923",
    backgroundGradientTo: "#08130D",
    color: (opacity = 1) => `rgba(26, 255, 146, ${opacity})`,
  }
  const renderItem = ({ item, index }) => (
    <Pressable
      style={{
        flexDirection: "row",
        marginTop: heightPercentageToDP(1),
        paddingBottom: 4,
      }}
      onPress={() =>
        navigation.navigate("ActivityList", {
          isDeals: null,
          filterStatus: item.enum_value,
          filterTargetId: data[0].id,
        })
      }
    >
      <Text style={{ width: widthPercentageToDP(16) }}>{item.enum_value}</Text>
      <Progress.Bar
        borderRadius={10}
        progress={item.value / percentage}
        color={
          item.enum_value === "HOT"
            ? "#E53935"
            : item.enum_value === "COLD"
            ? "#0553B7"
            : item.enum_value === "WARM"
            ? "#FFD13D"
            : item.enum_value === "CLOSED"
            ? "#c4c4c4"
            : "white"
        }
        borderWidth={0}
        height={17}
        width={widthPercentageToDP("50%")}
      />
      <Text ml={8} textAlign="right" style={{ width: widthPercentageToDP(16) }}>
        {item.value}
      </Text>
    </Pressable>
  )
  return (
    <Div mb={10} p={10} bg="white" shadow="sm" rounded={8} mx={19}>
      <Text fontSize={12}>Total Follow Up</Text>
      {data.map((e) => {
        var myArray = []

        for (let i = 0; i < e.breakdown.length; i++) {
          var color =
            e.breakdown[i].enum_value === "HOT"
              ? "#E53935"
              : e.breakdown[i].enum_value === "COLD"
              ? "#0553B7"
              : e.breakdown[i].enum_value === "WARM"
              ? "#FFD13D"
              : e.breakdown[i].enum_value === "CLOSED"
              ? "#c4c4c4"
              : "white"

          myArray.push({
            name: e.breakdown[i]?.enum_value,
            color: color,
            value: e.breakdown[i]?.value,
            legendFontColor: "#7F7F7F",
            legendFontSize: 15,
          })
        }
        return (
          <>
            {/* <PieChart
              data={myArray}
              width={widthPercentageToDP(85)}
              height={220}
              chartConfig={chartConfig}
              accessor="value"
              backgroundColor="transparent"
              paddingLeft="15"
            /> */}

            <Text fontWeight="bold" fontSize={16}>
              {e.value.value}
            </Text>

            <FlatList data={e.breakdown} renderItem={renderItem} />
          </>
        )
      })}
    </Div>
  )
}

export default FollowSection
