import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import Case from "case"
import React, { useState } from "react"
import { Pressable, TouchableOpacity, useWindowDimensions } from "react-native"
import { Div, Icon } from "react-native-magnus"

import Image from "components/Image"
import Tag from "components/Tag"
import Text from "components/Text"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatDate, responsive } from "helper"
import { COLOR_DISABLED } from "helper/theme"

import { Activity, activityStatusConfig } from "types/Activity"
import { orderPaymentStatusConfig, orderStatusConfig } from "types/Order"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, "CustomerDetail">,
  BottomTabNavigationProp<MainTabParamList>
>

type PropTypes = {
  activityData: Activity
  isDeals: boolean
  onPress?: () => void
}

export default ({ activityData, isDeals, onPress }: PropTypes) => {
  const { width: screenWidth } = useWindowDimensions()

  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [showComment, setShowComment] = useState(false)

  const {
    id,
    updatedAt,
    channel,
    lead,
    followUpMethod,
    status,
    activityCommentCount,
    latestComment,
    order,
  } = activityData || {}
  return (
    <TouchableOpacity
      onPress={
        onPress ||
        (() => {
          navigation.navigate("ActivityDetail", { id, isDeals })
        })
      }
    >
      <Div
        p={20}
        pb={!!latestComment ? 0 : 20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor={COLOR_DISABLED}
      >
        <Div row justifyContent="space-between" alignItems="flex-start">
          <Div maxW={0.5 * screenWidth}>
            <Text fontSize={10} color={COLOR_DISABLED} mb={5}>
              {order ? order?.invoiceNumber : Case.title(followUpMethod)}
            </Text>
            <Text mb={5}>{formatDate(updatedAt)}</Text>
            <Text fontSize={14} fontWeight="bold" mb={5}>
              {lead.label}
            </Text>
          </Div>
          {!!channel && (
            <Div
              borderWidth={2}
              borderColor="primary"
              py={5}
              px={10}
              maxW={0.35 * screenWidth}
            >
              <Text fontWeight="bold" textAlign="center">
                {Case.title(channel.name)}
              </Text>
            </Div>
          )}
        </Div>

        <Div row justifyContent="space-between">
          <Div row alignItems="center">
            {order ? (
              <>
                <Icon
                  name="card-outline"
                  fontSize={16}
                  fontFamily="Ionicons"
                  color="primary"
                />
                <Tag
                  containerColor={"#FFFFFF"}
                  textColor={
                    orderPaymentStatusConfig[activityData.order?.paymentStatus]
                      .textColor
                  }
                >
                  {
                    orderPaymentStatusConfig[activityData.order?.paymentStatus]
                      .displayText
                  }
                </Tag>
              </>
            ) : (
              <Text></Text>
            )}
          </Div>

          <Text fontWeight="bold" color={COLOR_DISABLED}>
            {Case.title(activityData?.user?.name)}
          </Text>
        </Div>

        {!!order && (
          <Div mb={10}>
            <Div row alignItems="center">
              <>
                <Image
                  width={responsive(16)}
                  scalable
                  source={require("assets/quotation_icon.png")}
                />
                <Tag
                  containerColor={"#FFFFFF"}
                  textColor={
                    orderStatusConfig[activityData.order?.status].textColor
                  }
                >
                  {orderStatusConfig[activityData.order?.status].displayText}
                </Tag>
              </>
            </Div>
          </Div>
        )}

        <Div row justifyContent="space-between" alignItems="center">
          <Div
            mx={-20}
            py={5}
            px={10}
            w={"30%"}
            bg={activityStatusConfig[status].bg}
            roundedRight="sm"
          >
            <Text
              textAlign="center"
              color={activityStatusConfig[status].textColor}
            >
              {activityStatusConfig[status].displayText}
            </Text>
          </Div>
          {activityCommentCount !== 0 && (
            <Text color={COLOR_DISABLED}>
              {activityCommentCount} comment(s)
            </Text>
          )}
        </Div>
        {!!latestComment && (
          <Pressable
            style={{ width: "100%", paddingVertical: 8 }}
            onPress={() => setShowComment((x) => !x)}
          >
            <Icon
              name={!showComment ? "chevron-down" : "chevron-up"}
              fontSize={14}
              fontFamily="FontAwesome5"
              color="black"
            />
          </Pressable>
        )}
        {showComment && (
          <>
            <Text fontSize={14} mb={5}>
              Latest Comment
            </Text>
            <Div row justifyContent="flex-start" mb={20}>
              <Text fontWeight="bold">
                {activityData.latestComment?.user?.name || "User"}:
              </Text>

              <Div flex={1} ml={5} p={10} bg={COLOR_DISABLED} rounded={4}>
                <Text>{activityData?.latestComment?.content}</Text>
              </Div>
            </Div>
          </>
        )}
      </Div>
    </TouchableOpacity>
  )
}
