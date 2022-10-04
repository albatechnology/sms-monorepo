import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { TouchableOpacity, useWindowDimensions } from "react-native"
import { Div } from "react-native-magnus"
import { RFValue } from "react-native-responsive-fontsize"

import Image from "components/Image"
import Text from "components/Text"
import WYSIWYG from "components/WYSIWYG"

import { MainTabParamList, PromoStackParamList } from "Router/MainTabParamList"

import { formatDate } from "helper"
import s from "helper/theme"

import { Promo } from "types/Promo"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  BottomTabNavigationProp<MainTabParamList, "Promo">,
  StackNavigationProp<PromoStackParamList>
>

type PropTypes = {
  promo: Promo
}

export default ({
  promo: { id, name, description, images, startTime, endTime },
}: PropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const { width: screenWidth } = useWindowDimensions()
  const imageWidth = screenWidth - RFValue(20) * 2

  return (
    <TouchableOpacity
      onPress={() =>
        navigation.navigate("PromoDetail", {
          id,
          name,
          description,
          images,
          startTime,
          endTime,
        })
      }
    >
      <Div mb={20}>
        <Image
          width={imageWidth}
          scalable
          source={{
            uri: images?.length > 0 ? images[0].url : null,
          }}
          style={[s.mB10, { borderRadius: 8 }]}
        />
        <Text fontWeight="bold">{name}</Text>
        {/* <Text fontWeight="bold" mb={5}>
          {formatDate(startTime)} - {formatDate(endTime)}
        </Text> */}
        <WYSIWYG body={description} />
      </Div>
    </TouchableOpacity>
  )
}
