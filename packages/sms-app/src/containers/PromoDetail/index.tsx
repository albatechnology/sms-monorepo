import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  RouteProp,
  useRoute,
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import { LinearGradient } from "expo-linear-gradient"
import React from "react"
import {
  FlatList,
  ScrollView,
  TouchableOpacity,
  useWindowDimensions,
} from "react-native"
import { Div } from "react-native-magnus"
import { RFValue } from "react-native-responsive-fontsize"
import { widthPercentageToDP } from "react-native-responsive-screen"

import Image from "components/Image"
import ProductCard from "components/ProductCard"
import Text from "components/Text"
import WYSIWYG from "components/WYSIWYG"

import { EntryStackParamList } from "Router/EntryStackParamList"
import { MainTabParamList, PromoStackParamList } from "Router/MainTabParamList"

import { formatDate } from "helper"
import s from "helper/theme"

type CurrentScreenRouteProp = RouteProp<PromoStackParamList, "PromoDetail">
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<PromoStackParamList, "PromoDetail">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const promoId = route?.params?.id ?? -1
  if (promoId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Main")
    }
    // toast(Languages.PageNotFound)
    return null
  }

  const { width: screenWidth } = useWindowDimensions()

  return (
    <ScrollView
      contentContainerStyle={{ flexGrow: 1, backgroundColor: "white" }}
      showsVerticalScrollIndicator={false}
      bounces={false}
    >
      <Image
        width={screenWidth}
        source={{
          uri:
            route.params.images?.length > 0 ? route.params.images[0].url : null,
        }}
        style={[s.mB10, { height: 215 }]}
      />
      <Div px={20} py={30}>
        <Text fontWeight="bold" mb={5}>
          Promo Name
        </Text>
        <Text mb={20}>{route.params.name}</Text>
        <Text fontWeight="bold" mb={5}>
          Period
        </Text>
        <Text fontWeight="normal" mb={20}>
          {formatDate(route.params.startTime)} -{" "}
          {formatDate(route.params.endTime)}
        </Text>

        <Text fontWeight="bold" mb={5}>
          Term and Condition:
        </Text>
        <WYSIWYG body={route.params.description} />
        {/* <Div row justifyContent="space-between">
          <TouchableOpacity disabled={true}>
            <Div
              bg="white"
              borderWidth={1}
              borderColor="#17949D"
              style={{
                height: 40,
                justifyContent: "center",
                borderRadius: 4,
                width: widthPercentageToDP(40),
              }}
            >
              <Text color="#17949D" fontSize={12} textAlign="center">
                Catalogue
              </Text>
            </Div>
          </TouchableOpacity>
          <TouchableOpacity disabled={true}>
            <LinearGradient
              style={{
                height: 40,
                justifyContent: "center",
                borderRadius: 4,
                width: widthPercentageToDP(40),
              }}
              locations={[0.5, 1.0]}
              colors={["#20B5C0", "#17949D"]}
            >
              <Text color="white" fontSize={12} textAlign="center">
                Show Product
              </Text>
            </LinearGradient>
          </TouchableOpacity>
        </Div> */}
        <Div mx={"-20"}></Div>
      </Div>
    </ScrollView>
  )
}
