import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useState } from "react"
import { Pressable } from "react-native"
import { Div, Avatar, Icon, Text, Button } from "react-native-magnus"
import Modal from "react-native-modal"

import LeadDropdownInput from "components/LeadDropdownInput"

import useMultipleQueries from "hooks/useMultipleQueries"

import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { responsive } from "helper"
import { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

import { Customer, getFullName, getInitials } from "types/Customer"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "CustomerDetail">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

type PropTypes = {
  customer: Customer
}

export default function TopSection({ customer }: PropTypes) {
  const {
    queries: [{ data: userData }],
    meta: { isError, isFetching, refetch },
  } = useMultipleQueries([useUserLoggedInData()] as const)
  const [modalVisible, setModalVisible] = useState(false)
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  return (
    <Div p={20} bg="primary">
      <Div row>
        <Avatar
          bg={COLOR_DISABLED}
          color={COLOR_PRIMARY}
          size={responsive(38)}
          mr={10}
        >
          {getInitials(customer)}
        </Avatar>
        <Div
          row
          flex={1}
          justifyContent="space-between"
          alignItems="flex-start"
        >
          <Div flex={1}>
            <Text fontSize={14} color="white" mb={5}>
              {getFullName(customer)}
            </Text>
            <Text color="white" mb={5}>
              {customer.phone}
            </Text>
          </Div>
          {userData.type === "DIRECTOR" &&
          userData.app_create_lead === false ? null : (
            <Div row>
              <Pressable
                onPress={() =>
                  navigation.navigate("EditCustomer", { id: customer.id })
                }
              >
                <Icon
                  bg="primary"
                  p={5}
                  name="edit"
                  color="white"
                  fontSize={16}
                  fontFamily="FontAwesome5"
                />
              </Pressable>
              <Pressable onPress={() => setModalVisible(true)}>
                <Icon
                  bg="primary"
                  p={5}
                  name="shopping-bag"
                  color="white"
                  fontSize={16}
                  fontFamily="FontAwesome5"
                />
              </Pressable>
            </Div>
          )}
        </Div>
      </Div>
      <LeadSelectorModal visible={modalVisible} setVisible={setModalVisible} />
    </Div>
  )
}

const LeadSelectorModal = ({ visible = false, setVisible = (val) => {} }) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [leadId, setLeadId] = useState(null)

  return (
    <Modal
      useNativeDriver
      isVisible={visible}
      onBackdropPress={() => setVisible(false)}
    >
      <Div bg="white" p={20}>
        <Text mb={10}>Please select a lead/prospect</Text>
        <LeadDropdownInput value={leadId} onSelect={setLeadId} />
        <Button
          mt={20}
          block
          onPress={() => {
            setVisible(false)
            navigation.navigate("Checkout", { leadId: leadId })
          }}
          bg="primary"
          borderColor="primary"
          borderWidth={0.8}
          alignSelf="center"
          disabled={!leadId}
        >
          Proceed to checkout
        </Button>
      </Div>
    </Modal>
  )
}
