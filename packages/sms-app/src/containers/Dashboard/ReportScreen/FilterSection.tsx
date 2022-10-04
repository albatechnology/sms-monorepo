import { useNavigation } from "@react-navigation/native"
import React from "react"
import { Image, StyleSheet, View } from "react-native"
import { Button, Div, Text } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"

import DatePickerInput from "components/DatePickerInput"

import theme from "helper/theme"

type PropsType = {
  isSales?: boolean
}

const FilterSection = ({ isSales }: PropsType) => {
  const navigation = useNavigation()
  return (
    <View>
      <Div row mx={20} alignItems="center" justifyContent="space-between">
        <Div row>
          <Image
            source={require("../../../assets/Report.png")}
            style={{ width: 17, height: 22 }}
          />
          <Text
            style={{ marginLeft: widthPercentageToDP(2) }}
            fontSize={14}
            fontWeight="bold"
          >
            Report
          </Text>
        </Div>
        {isSales === true ? null : (
          <Button
            bg="transparent"
            onPress={() => navigation.navigate("FilterScreen")}
          >
            <Image source={require("../../../assets/dot.png")} />
          </Button>
        )}
      </Div>
    </View>
  )
}

export default FilterSection

const styles = StyleSheet.create({})
