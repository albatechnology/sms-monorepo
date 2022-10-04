import React from "react"
import { View, Text, TouchableOpacity } from "react-native"
import { Icon, Tooltip } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"

const TipHelper = ({ onPress, label }) => {
  const tooltipRef = React.createRef()
  return (
    <>
      <TouchableOpacity onPress={onPress}>
        <Icon
          ml={5}
          name="info"
          color="grey"
          fontFamily="Feather"
          fontSize={12}
        />
      </TouchableOpacity>
      <Tooltip ref={tooltipRef} mr={widthPercentageToDP(10)} text={label} />
    </>
  )
}

export default TipHelper
