import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { Button } from "react-native-magnus"

import { EntryStackParamList } from "Router/EntryStackParamList"
import { MainTabParamList } from "Router/MainTabParamList"

import { ReportTarget } from "types/ReportTarget"

export type ReportInfoExtrasPropTypes = {
  reportTarget: ReportTarget
}

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<EntryStackParamList>,
  BottomTabNavigationProp<MainTabParamList>
>

// Other random stuff for each target
export const ReportInfoExtras = ({
  reportTarget,
}: ReportInfoExtrasPropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  if (reportTarget.type === "DEALS_INVOICE_PRICE") {
    const handleClick = () => {
      navigation.navigate("TableRevenue", {
        isDeals: true,
        filterStatus: null,
        filterTargetId: reportTarget.id,
      })
    }
    return (
      <Button
        alignSelf="flex-end"
        bg="primary"
        mt={2}
        px={10}
        py={3}
        onPress={handleClick}
      >
        Detail
      </Button>
    )
  }

  return null
}
