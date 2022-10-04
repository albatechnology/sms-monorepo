import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { Pressable } from "react-native"
import { Div } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"
import { PieChart } from "react-native-svg-charts"

import Tag from "components/Tag"
import Text from "components/Text"

import { ActivityStatus } from "api/generated/enums"

import { EntryStackParamList } from "Router/EntryStackParamList"
import { MainTabParamList } from "Router/MainTabParamList"

import { ReportTargetBreakdown } from "types/ReportTargetBreakdown"
import { enumConfigMapping } from "types/enumConfigMapping"

export type ReportInfoBreakdownPropTypes = {
  breakdownList: ReportTargetBreakdown[] | null
  targetId: number
}

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<EntryStackParamList>,
  BottomTabNavigationProp<MainTabParamList>
>
export const ReportInfoBreakdown = ({
  breakdownList,
  targetId,
}: ReportInfoBreakdownPropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  if (
    !breakdownList ||
    breakdownList.length === 0 ||
    !Object.keys(enumConfigMapping).includes(breakdownList[0].enumType)
  ) {
    return null
  }

  const config = enumConfigMapping[breakdownList[0].enumType]

  const handleClick = (config: any, breakdown: ReportTargetBreakdown) => {
    if (breakdown.enumType === "ACTIVITY_STATUS") {
      navigation.navigate("ActivityList", {
        isDeals: null,
        filterStatus: breakdown.enumValue as ActivityStatus,
        filterTargetId: targetId,
      })
      return
    }
  }

  var percentage = breakdownList.reduce((n, { value }) => n + value, 0)

  return (
    <Div pt={6} justifyContent="space-between" bg="white">
      {breakdownList
        .sort((a, b) => config[a.enumValue].order - config[b.enumValue].order)
        .map((breakdown, i) => {
          return (
            <Pressable
              key={i}
              onPress={(e) => {
                e.stopPropagation()
                handleClick(config, breakdown)
              }}
            >
              <Tag
                containerColor={config[breakdown.enumValue].bg}
                textColor={config[breakdown.enumValue].textColor}
              >
                {breakdown.value} {config[breakdown.enumValue].displayText}
              </Tag>
              <Div
                style={{
                  marginTop: heightPercentageToDP(1),
                  flexDirection: "row",
                  justifyContent: "space-between",
                }}
              >
                <Text w={55}>{config[breakdown.enumValue].displayText}</Text>
                <Progress.Bar
                  borderRadius={10}
                  progress={breakdown.value / percentage}
                  color={config[breakdown.enumValue].bg}
                  borderWidth={0}
                  height={17}
                  width={widthPercentageToDP("57%")}
                />
                <Text style={{ marginLeft: widthPercentageToDP(2) }}>
                  {breakdown.value}
                </Text>
              </Div>
            </Pressable>
          )
        })}
    </Div>
  )
}
