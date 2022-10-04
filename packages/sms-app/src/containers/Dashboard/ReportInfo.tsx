import React, { useEffect, useState } from "react"
import { Pressable } from "react-native"
import { Div } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import ProgressBar from "components/ProgressBar"
import Text from "components/Text"

import { COLOR_DISABLED } from "helper/theme"

import {
  calculateProgressBarColor,
  ReportTarget,
  targetTypeConfig,
} from "types/ReportTarget"

import BarChart from "./BarChart"
import { ReportInfoBreakdown } from "./ReportInfoBreakdown"
import { ReportInfoExtras } from "./ReportInfoExtras"

export type ReportInfoPropTypes = {
  reportTarget: ReportTarget
  onPress?: () => void
}

export const ReportInfo = ({ reportTarget, onPress }: ReportInfoPropTypes) => {
  const percentage = !!reportTarget.target
    ? reportTarget.value / reportTarget.target
    : // ? parseFloat(
      //     ((reportTarget.value / reportTarget.target ?? 1) * 100).toFixed(2),
      //   )
      0
  const [color, setColor] = useState("grey")
  useEffect(() => {
    if (reportTarget.type === "DEALS_INVOICE_PRICE") {
      setColor("#2DCC70")
    } else if (reportTarget.type === "ORDER_SETTLEMENT_COUNT") {
      setColor("#E53935")
    } else if (reportTarget.type === "DEALS_BRAND_PRICE") {
      setColor("#17949D")
    }
  }, [])
  return (
    <Pressable onPress={onPress}>
      <Div
        justifyContent="flex-start"
        alignItems="flex-start"
        bg="white"
        w={widthPercentageToDP("80%")}
      >
        <Text fontSize={14} mb={5} color={COLOR_DISABLED}>
          {targetTypeConfig[reportTarget.type].displayText}{" "}
          {reportTarget?.report?.name}
        </Text>
        <Text fontSize={18} fontWeight="bold" mb={5}>
          {targetTypeConfig[reportTarget.type].formatValue(reportTarget.value)}
        </Text>
        {reportTarget.chartType === "SINGLE" && (
          // <ProgressBar
          //   current={reportTarget.value}
          //   target={reportTarget.target}
          //   color={calculateProgressBarColor(percentage)}
          //   mb={5}
          // />
          <Progress.Circle
            style={{
              position: "absolute",
              marginLeft: widthPercentageToDP(65),
              marginTop: heightPercentageToDP(1),
            }}
            unfilledColor="#F9F9F9"
            borderWidth={0}
            size={60}
            progress={percentage}
            animated={false}
            thickness={10}
            showsText={true}
            color={color}
          />
        )}
        {reportTarget.chartType === "MULTIPLE" && (
          <BarChart targetLines={reportTarget.targetLines} />
        )}
        {reportTarget.chartType === "SINGLE" && (
          <Div row justifyContent="space-between">
            <Text color={COLOR_DISABLED}>
              From Target{" "}
              {targetTypeConfig[reportTarget.type].formatValue(
                reportTarget.target,
              )}
              {targetTypeConfig[reportTarget.type].formatValue(
                reportTarget.target,
              )}
            </Text>
            <Text color={COLOR_DISABLED}>{percentage}%</Text>
          </Div>
        )}

        <ReportInfoBreakdown
          breakdownList={reportTarget.breakdown}
          targetId={reportTarget.id}
        />

        <ReportInfoExtras reportTarget={reportTarget} />
      </Div>
    </Pressable>
  )
}
