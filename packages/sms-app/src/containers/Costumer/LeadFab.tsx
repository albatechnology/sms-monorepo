import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { Fab, Div, Image, Button } from "react-native-magnus"

import Text from "components/Text"

import { LeadType } from "api/generated/enums"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { responsive } from "helper"
import { COLOR_DISABLED } from "helper/theme"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "CustomerList">,
  BottomTabNavigationProp<MainTabParamList>
>

type PropTypes = {
  type: LeadType
  isUnhandled?: boolean
}

export default function LeadFab({ type, isUnhandled = false }: PropTypes) {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  return (
    <Fab bg="#17949D" fontSize={12} h={50} w={50} shadow="sm">
      <Button
        p="none"
        bg="transparent"
        justifyContent="flex-end"
        alignSelf="flex-end"
        onPress={() => navigation.navigate("AddLead", { type, isUnhandled })}
      >
        <Div rounded="sm" bg={COLOR_DISABLED} p="sm">
          <Text fontSize="md">Existing Customer</Text>
        </Div>
        <Image
          h={responsive(36)}
          w={responsive(36)}
          ml={10}
          rounded="circle"
          source={require("assets/icon_existing_cust.png")}
        />
      </Button>
      <Button
        p="none"
        bg="transparent"
        justifyContent="flex-end"
        alignSelf="flex-end"
        onPress={() =>
          navigation.navigate("AddLeadWithCustomer", { type, isUnhandled })
        }
      >
        <Div rounded="sm" bg={COLOR_DISABLED} p="sm">
          <Text fontSize="md">New Customer</Text>
        </Div>
        <Image
          h={responsive(36)}
          w={responsive(36)}
          ml={10}
          rounded="circle"
          source={require("assets/icon_new_cust.png")}
        />
      </Button>
    </Fab>
  )
}
