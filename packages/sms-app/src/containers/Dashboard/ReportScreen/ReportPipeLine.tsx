import { useNavigation, useRoute } from "@react-navigation/native"
import { LinearGradient } from "expo-linear-gradient"
import React from "react"
import { TouchableOpacity } from "react-native"
import { Div, Image, Text } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

const ReportPipeLine = () => {
  const navigation = useNavigation()
  const route = useRoute()
  return (
    <Div flex={1} bg="#fff">
      <Image
        mt={heightPercentageToDP(5)}
        p={heightPercentageToDP(20)}
        resizeMode="contain"
        w={widthPercentageToDP(100)}
        source={require("../../../assets/ReportPipeLine.png")}
      />
      <Div row justifyContent="space-evenly" mt={heightPercentageToDP(4)}>
        <TouchableOpacity
          onPress={() =>
            navigation.navigate("PipeLineScreen", route?.params?.userData)
          }
        >
          <LinearGradient
            style={{
              height: 40,
              width: widthPercentageToDP(40),
              justifyContent: "center",
              borderRadius: 4,
            }}
            locations={[0.5, 1.0]}
            colors={["#20B5C0", "#17949D"]}
          >
            <Text color="white" fontSize={14} textAlign="center">
              Pipeline
            </Text>
          </LinearGradient>
        </TouchableOpacity>
        <TouchableOpacity
          onPress={() => navigation.navigate("ReportCardScreen")}
        >
          <LinearGradient
            style={{
              height: 40,
              width: widthPercentageToDP(40),
              justifyContent: "center",
              borderRadius: 4,
            }}
            locations={[0.5, 1.0]}
            colors={["#20B5C0", "#17949D"]}
          >
            <Text color="white" fontSize={14} textAlign="center">
              Brand
            </Text>
          </LinearGradient>
        </TouchableOpacity>
      </Div>
    </Div>
  )
}

export default ReportPipeLine
