import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import Case from "case"
import React, { useState } from "react"
import { Pressable, ScrollView } from "react-native"
import { Button, Div, Icon, Overlay } from "react-native-magnus"

import Error from "components/Error"
import InfoBlock from "components/InfoBlock"
import Tag from "components/Tag"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useAddressById from "api/hooks/address/useAddressById"
import useLeadEditMutation from "api/hooks/lead/useLeadEditMutation"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatDate, formatDateOnly } from "helper"

import { activityStatusConfig } from "types/Activity"
import { Lead, leadStatusConfig } from "types/Lead"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "CustomerDetail">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

type PropTypes = {
  lead: Lead
}

export default ({ lead }: PropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const [typeAction, setTypeAction] = useState("")
  const [overlayVisible, setOverlayVisible] = useState(false)

  const {
    id,
    customer,
    label,
    channel,
    status,
    type,
    latestActivity,
    interest,
  } = lead
  const {
    id: customerId,
    title,
    firstName,
    lastName,
    dateOfBirth,
    phone,
    email,
    defaultAddressId,
  } = customer

  const [editLead] = useLeadEditMutation()
  const {
    queries: [{ data: addressData }, { data: userData }],
    meta: { isError, isFetching, refetch },
  } = useMultipleQueries([
    useAddressById(defaultAddressId),
    useUserLoggedInData(),
  ] as const)

  const { addressLine1, addressLine2, addressLine3, city, province, postcode } =
    addressData || {}

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  return (
    <>
      <Overlay visible={overlayVisible} p={20}>
        <Text mb={10}>Are you sure? This action is irreversible.</Text>
        <Div row justifyContent="space-between">
          <Button
            flex={1}
            bg="primary"
            color="white"
            borderWidth={1}
            borderColor="primary"
            mr={10}
            onPress={() => {
              setOverlayVisible(false)
              editLead({
                id: id,
                type: typeAction,
                label: label,
                customerId: customerId,
              })
            }}
          >
            Yes
          </Button>
          <Button
            flex={1}
            bg="white"
            color="primary"
            borderWidth={1}
            borderColor="primary"
            onPress={() => setOverlayVisible(false)}
          >
            No
          </Button>
        </Div>
      </Overlay>
      <ScrollView
        contentContainerStyle={{ flexGrow: 1, backgroundColor: "white" }}
        showsVerticalScrollIndicator={false}
        bounces={false}
      >
        {/* Lead */}
        <Div row justifyContent="space-between" px={20} mt={30}>
          <Text fontSize={14} fontWeight="bold">
            Lead
          </Text>
          <Pressable
            onPress={() => navigation.navigate("EditLead", { id: lead.id })}
          >
            <Icon
              bg="white"
              p={5}
              name="edit"
              color="primary"
              fontSize={16}
              fontFamily="FontAwesome5"
            />
          </Pressable>
        </Div>
        <InfoBlock
          title="Priority"
          data={
            !!status && (
              <Tag
                containerColor={leadStatusConfig[status].bg}
                textColor={leadStatusConfig[status].textColor}
              >
                {leadStatusConfig[status].displayText}
              </Tag>
            )
          }
        />
        <InfoBlock title="Type" data={Case.title(type)} />
        <InfoBlock title="Channel" data={channel.name} length="channel" />
        <InfoBlock title="Label" data={label} />
        <InfoBlock title="Interest" data={interest} />

        {/* Lastest Activity */}
        {latestActivity ? (
          <>
            <Text fontSize={14} fontWeight="bold" px={20} mt={30}>
              Lastest Activity
            </Text>
            <InfoBlock
              title="Status"
              data={
                !!latestActivity.status && (
                  <Tag
                    rounded={20}
                    containerColor={
                      activityStatusConfig[latestActivity.status].bg
                    }
                    textColor={
                      activityStatusConfig[latestActivity.status].textColor
                    }
                  >
                    {activityStatusConfig[latestActivity.status].displayText}
                  </Tag>
                )
              }
            />
            <InfoBlock
              title="Last Update"
              data={formatDate(latestActivity.updatedAt)}
            />
            <InfoBlock
              title="Created"
              data={formatDate(latestActivity.createdAt)}
            />
          </>
        ) : null}

        <Text fontSize={14} fontWeight="bold" px={20} mt={30}>
          Basic Information
        </Text>
        <InfoBlock title="Title" data={Case.title(title)} />
        <InfoBlock title="First Name" data={firstName} />
        <InfoBlock title="Last Name" data={lastName} />
        {!!dateOfBirth && (
          <InfoBlock title="DoB" data={formatDateOnly(dateOfBirth)} />
        )}
        <InfoBlock title="Phone Number" data={phone} />
        <InfoBlock title="Email" data={email} />

        <Text fontSize={14} fontWeight="bold" px={20} mt={30}>
          Default Address
        </Text>
        <InfoBlock title="Address Line 1" data={addressLine1} />
        {!!addressLine2 && (
          <InfoBlock title="Address Line 2" data={addressLine2} />
        )}
        {!!addressLine3 && (
          <InfoBlock title="Address Line 3" data={addressLine3} />
        )}
        <InfoBlock title="City" data={city} />
        <InfoBlock title="Province" data={province} />
        <InfoBlock title="Postcode" data={postcode} />
        {type === "LEADS" && (
          <>
            <Button
              block
              m={20}
              bg="primary"
              color="white"
              borderWidth={0}
              disabled={
                userData.type === "DIRECTOR" &&
                userData.app_create_lead === false
                  ? true
                  : false
              }
              onPress={async () => {
                await setTypeAction("PROSPECT")
                setOverlayVisible(true)
              }}
            >
              Upgrade To Prospect
            </Button>
            <Button
              block
              m={20}
              mt={-10}
              bg="#d63031"
              color="white"
              borderWidth={0}
              disabled={
                userData.type === "DIRECTOR" &&
                userData.app_create_lead === false
                  ? true
                  : false
              }
              onPress={async () => {
                await setTypeAction("DROP")
                setOverlayVisible(true)
              }}
            >
              Drop Lead
            </Button>
          </>
        )}
        {type === "PROSPECT" && (
          <Button
            block
            m={20}
            bg="#d63031"
            color="white"
            borderWidth={0}
            disabled={
              userData.type === "DIRECTOR" && userData.app_create_lead === false
                ? true
                : false
            }
            onPress={async () => {
              await setTypeAction("DROP")
              setOverlayVisible(true)
            }}
          >
            Drop Lead
          </Button>
        )}
      </ScrollView>
    </>
  )
}
