import React from "react"
import { View, Image, TouchableOpacity } from "react-native"
import { Div, Icon, Text, Tooltip } from "react-native-magnus"
import * as Progress from "react-native-progress"
import { widthPercentageToDP } from "react-native-responsive-screen"

import { formatCurrency, responsive } from "helper"

const Estimated = () => {
  const tooltipRef = React.createRef()
  const Leads = () => (
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
          <Text fontSize={responsive(10)}>Estimated</Text>
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
            text={`Jumlah nominal estimasi yang diinput pada saat setiap follow up dibuat`}
          />
        </Div>
        <Div row alignItems="center">
          <Text fontSize={responsive(12)} my={5} fontWeight="bold">
            {formatCurrency(120000)}
          </Text>
        </Div>
      </Div>
      <Image
        source={require("../../../assets/calc.png")}
        style={{ width: 40, resizeMode: "contain", marginTop: -20 }}
      />
    </Div>
  )
  return (
    <Div alignItems="center">
      <Leads />
    </Div>
  )
}

export default Estimated
