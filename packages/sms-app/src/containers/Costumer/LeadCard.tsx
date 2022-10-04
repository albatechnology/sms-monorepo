import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import moment from "moment"
import React, { useState } from "react"
import {
  Linking,
  Platform,
  Pressable,
  TouchableOpacity,
  Image,
} from "react-native"
import { Avatar, Button, Div, Icon, Modal } from "react-native-magnus"

import Text from "components/Text"
import UserDropdownInput from "components/UserDropdownInput"

import useLeadAssignMutation from "api/hooks/lead/useLeadAssignMutation"

import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { responsive } from "helper"
import { COLOR_PRIMARY, COLOR_DISABLED } from "helper/theme"

import { getInitials, getFullName } from "types/Customer"
import { Lead, leadStatusConfig } from "types/Lead"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<CustomerStackParamList, any>,
  BottomTabNavigationProp<MainTabParamList>
>

type PropTypes = {
  lead: Lead
  isUnhandled?: boolean
}

export default ({ lead, isUnhandled = false }: PropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [assignModalOpened, setAssignModalOpened] = useState(false)
  const [assignLead, { isLoading }] = useLeadAssignMutation()

  const [selectedUser, setSelectedUser] = useState(null)

  const onHideModal = () => {
    setAssignModalOpened(false)
  }

  const openLink = (link) => {
    if (!!link) {
      Linking.canOpenURL(link)
        .then((supported) => {
          if (supported) {
            return Linking.openURL(link)
          } else {
            toast("App is not found")
          }
        })
        .catch((err) => toast("Something went wrong"))
    }
  }
  return (
    <TouchableOpacity
      style={{
        shadowColor: "#000",
        shadowOffset: {
          width: 0,
          height: 1,
        },
        shadowOpacity: 0.22,
        shadowRadius: 2.22,

        elevation: 3,
      }}
      onPress={() => navigation.navigate("CustomerDetail", { leadId: lead.id })}
    >
      <Div
        p={20}
        bg="white"
        mx={8}
        mb={10}
        rounded={8}
        borderBottomWidth={0.8}
        borderBottomColor={COLOR_DISABLED}
        overflow="hidden"
      >
        <Div row mb={10}>
          <Avatar
            bg={leadStatusConfig[lead.status].bg}
            color="white"
            size={responsive(32)}
            mr={10}
          >
            {getInitials(lead.customer)}
          </Avatar>
          <Div
            row
            flex={1}
            justifyContent="space-between"
            alignItems="flex-start"
          >
            <Div flex={1}>
              <Text
                fontSize={14}
                fontWeight="bold"
                mb={5}
                mr={5}
                numberOfLines={1}
              >
                {getFullName(lead.customer)}
              </Text>
              {!!lead.customer.phone && (
                <Text
                  fontWeight="bold"
                  color="#DADADA"
                  mt={5}
                  numberOfLines={1}
                >
                  {lead.customer.phone}
                </Text>
              )}
              {!!lead.leadCategory && (
                <Text fontWeight="bold" mt={5} numberOfLines={1}>
                  Customer from {lead.leadCategory.name}
                </Text>
              )}
              {!!lead.leadSubCategory && (
                <Text fontWeight="normal" mt={5} numberOfLines={1}>
                  {lead.leadSubCategory?.name}
                </Text>
              )}
              {!lead.hasActivity && (
                <Text fontWeight="bold" mt={5} numberOfLines={1} color="red">
                  No Activity Yet
                </Text>
              )}
              {!!lead.hasActivity && (
                <Text
                  fontWeight="normal"
                  mt={5}
                  numberOfLines={1}
                  color="#c4c4c4"
                >
                  Last follow up {moment(lead.updatedAt).format("DD-MM-YYYY ")}
                </Text>
              )}
            </Div>
            <Div row>
              {lead.customer.phone && (
                <Pressable
                  onPress={() => {
                    const phoneNumber = `+62${lead.customer.phone.slice(1)}`
                    openLink(
                      Platform.OS === "android"
                        ? `whatsapp://send?text=Halo&phone=${phoneNumber}`
                        : `https://api.whatsapp.com/send?text=Halo&phone=${phoneNumber}`,
                    )
                  }}
                >
                  <Icon
                    bg="primary"
                    p={5}
                    mr={10}
                    rounded="circle"
                    name="logo-whatsapp"
                    color="white"
                    fontSize={16}
                    fontFamily="Ionicons"
                  />
                </Pressable>
              )}
              {lead.customer.email && (
                <Pressable
                  onPress={() => {
                    openLink(`mailto: ${lead.customer.email}`)
                  }}
                >
                  <Icon
                    bg="primary"
                    p={5}
                    mr={10}
                    rounded="circle"
                    name="mail"
                    color="white"
                    fontSize={16}
                    fontFamily="Ionicons"
                  />
                </Pressable>
              )}
              {lead.customer.phone && (
                <Pressable
                  onPress={() => {
                    openLink(`tel:${lead.customer.phone}`)
                  }}
                >
                  <Icon
                    bg="primary"
                    p={5}
                    rounded="circle"
                    name="call"
                    color="white"
                    fontSize={16}
                    fontFamily="Ionicons"
                  />
                </Pressable>
              )}
            </Div>
          </Div>
        </Div>
        <Div row justifyContent="space-between">
          <Div row alignItems="center" justifyContent="center">
            <Icon
              color="grey"
              name="person"
              fontSize={12}
              fontFamily="Ionicons"
              mr={5}
            />
            <Text color="grey">{lead.user.name}</Text>
          </Div>

          {isUnhandled && (
            <>
              <Button
                bg="primary"
                mr={20}
                ml={10}
                py={6}
                alignSelf="center"
                onPress={() => {
                  setAssignModalOpened(true)
                }}
              >
                <Text fontWeight="bold" color="white">
                  Assign
                </Text>
              </Button>
              <Modal
                useNativeDriver
                isVisible={assignModalOpened}
                animationIn="slideInUp"
                animationOut="slideOutDown"
                onBackdropPress={onHideModal}
                onModalHide={onHideModal}
                onBackButtonPress={onHideModal}
                h="40%"
              >
                <Div h="100%" px={20} pt={20}>
                  <Text mb={10}>
                    User<Text color="red">*</Text>
                  </Text>
                  <Div h={50}>
                    <UserDropdownInput
                      value={selectedUser}
                      onSelect={(val) => setSelectedUser(val)}
                    />
                  </Div>
                  <Button
                    bg="primary"
                    mx={20}
                    mt={10}
                    block
                    alignSelf="center"
                    disabled={!selectedUser}
                    onPress={() => {
                      assignLead({ id: lead.id, userId: selectedUser }, (x) =>
                        x.finally(() => {
                          onHideModal()
                        }),
                      )
                    }}
                    loading={isLoading}
                  >
                    <Text fontWeight="bold" color="white">
                      Assign to user
                    </Text>
                  </Button>
                </Div>
              </Modal>
            </>
          )}
          {!isUnhandled && (
            // <Div
            //   mx={-20}
            //   py={5}
            //   px={10}
            //   w={"25%"}
            //   roundedLeft="sm"
            //   bg={leadStatusConfig[lead.status].bg}
            // >
            //   <Text textAlign="center" color={"white"}>
            //     {leadStatusConfig[lead.status].displayText}
            //   </Text>
            // </Div>
            <Div row alignItems="center">
              <Image
                source={require("../../assets/Loc.png")}
                style={{ width: 11, resizeMode: "contain" }}
              />
              <Text ml={5} fontWeight="bold" color="#17949D" numberOfLines={1}>
                {lead.channel.name}
              </Text>
            </Div>
          )}
        </Div>
      </Div>
    </TouchableOpacity>
  )
}
