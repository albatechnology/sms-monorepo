import * as scale from "d3-scale"
import React from "react"
import { Div } from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Text from "components/Text"

import { responsive } from "helper"
import { formatCurrency } from "helper"
import { COLOR_PRIMARY } from "helper/theme"

export default ({ targetLines }) => {
  const contentInset = {
    top: responsive(10),
    bottom: responsive(10),
  }
  const randomColor = (
    "#" +
    ((Math.random() * 0xffffff) << 0).toString(16) +
    "000000"
  ).slice(0, 6)
  const containerHeight =
    targetLines.length > 0 ? targetLines.length * responsive(40) : 0

  const CUT_OFF =
    Math.max.apply(
      Math,
      targetLines.map((x) => x.value),
    ) / 2

  const data = targetLines.map((x) => ({
    value: x.value,
    label: x.label,
    svg: {
      fill: COLOR_PRIMARY,
    },
  }))

  const Labels = ({ x, y, bandwidth, data }) =>
    data.map((value, index) => (
      <Text
        key={index}
        x={value.value > CUT_OFF ? x(value.value) - 110 : x(value.value) + 10}
        y={y(index) + bandwidth / 2}
        fontSize={responsive(10)}
        fill={value.value > CUT_OFF ? "white" : "black"}
        alignmentBaseline={"middle"}
      >
        {formatCurrency(value.value)}
      </Text>
    ))

  return (
    <Div w="100%" flex={1} alignItems="center" justifyContent="center" row>
      {targetLines.map((target) => {
        return (
          <Div
            borderWidth={0.7}
            w={widthPercentageToDP(35)}
            h={60}
            borderColor="blue"
            style={{
              borderRadius: 8,
              marginHorizontal: widthPercentageToDP(4),
            }}
          >
            <Div
              style={{
                marginLeft: widthPercentageToDP(2),
                marginTop: heightPercentageToDP(1),
              }}
            >
              <Text fontSize={10} w={50}>
                {target.label}
              </Text>
              {/* <Div>
            <Progress.Bar borderRadius={10} progress={1} color={'lightgreen'} borderWidth={0} height={14} width={180} />
          </Div> */}
              <Text fontSize={12} fontWeight="bold">
                {formatCurrency(target.value)}
              </Text>
            </Div>
          </Div>
        )
      })}
      {/* <YAxis
        style={{ marginHorizontal: responsive(5) }}
        data={data}
        yAccessor={({ index }) => index}
        contentInset={contentInset}
        scale={scale.scaleBand}
        svg={{
          fill: COLOR_PRIMARY,
          fontSize: responsive(10),
        }}
        spacing={0.2}
        formatLabel={(_, index) => ` ${data[index].label} `} // Do not remove the spaces.
      />
      <BarChart
        style={{ flex: 1, marginLeft: responsive(5) }}
        data={data}
        horizontal={true}
        yAccessor={({ item }) => item.value}
        svg={{ fill: COLOR_PRIMARY }}
        contentInset={contentInset}
        spacing={0.2}
        gridMin={0}
      > */}
      {/* <Labels />
      </BarChart> */}
    </Div>
  )
}
