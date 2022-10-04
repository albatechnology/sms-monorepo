import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import Case from "case"
import React, { useEffect, useState } from "react"
import { FlatList, Keyboard, Pressable, RefreshControl } from "react-native"
import { Div, Icon } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"

import FooterLoading from "components/CommonList/FooterLoading"
import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Error from "components/Error"
import Image from "components/Image"
import InfoBlock from "components/InfoBlock"
import Loading from "components/Loading"
import OrderDemand from "components/Order/OrderDemand"
import OrderDetail from "components/Order/OrderDetail"
import Tag from "components/Tag"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import MessageForm from "forms/MessageForm"

import { customErrorHandler } from "api/errors"
import useActivityById from "api/hooks/activity/useActivityById"
import useActivityCommentCreateMutation from "api/hooks/activityComment/useActivityCommentCreateMutation"
import useActivityCommentListByActivity from "api/hooks/activityComment/useActivityCommentListByActivity"
import useOrderById from "api/hooks/order/useOrderById"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { formatCurrency, formatDate } from "helper"
import Languages from "helper/languages"
import { dataFromPaginated } from "helper/pagination"
import { COLOR_PRIMARY } from "helper/theme"
import s, { COLOR_DISABLED } from "helper/theme"

import { activityStatusConfig } from "types/Activity"
import { ActivityComment } from "types/ActivityComment"
import { getFullName } from "types/Customer"
import { leadStatusConfig } from "types/Lead"

import ActivityCommentItem from "./ActivityCommentItem"

type CurrentScreenRouteProp = RouteProp<
  CustomerStackParamList,
  "ActivityDetail"
>
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "ActivityDetail">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const activityId = route?.params?.id ?? -1
  if (activityId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }
  const isDeals = route?.params?.isDeals ?? false
  useEffect(() => {
    navigation.setOptions({
      headerRight: () => (
        <Pressable
          onPress={() => {
            navigation.navigate("EditActivity", { id: activityId })
          }}
        >
          <Icon
            name="edit"
            color="white"
            fontSize={16}
            fontFamily="FontAwesome5"
            mr={10}
          />
        </Pressable>
      ),
    })
  }, [navigation, activityId])

  const [createComment] = useActivityCommentCreateMutation()

  const {
    queries: [{ data: activityData }, { data: activityCommentPaginatedData }],
    meta: {
      isError: activityIsError,
      isLoading: activityIsLoading,
      isFetching: activityIsFetching,
      refetch: activityRefetch,
      hasNextPage: activityHasNextPage,
      fetchNextPage: activityFetchNextPage,
      isFetchingNextPage: activityIsFetchingNextPage,
    },
  } = useMultipleQueries([
    useActivityById(
      activityId,
      customErrorHandler({
        404: () => {
          toast("Activity tidak ditemukan")
          if (navigation.canGoBack()) {
            navigation.goBack()
          } else {
            navigation.navigate("Dashboard")
          }
        },
      }),
    ),
    useActivityCommentListByActivity({ activity: activityId, sort: "-id" }),
  ] as const)

  const {
    queries: [{ data: orderData }],
    meta: { isError: orderIsError, isLoading: orderIsLoading, refetch },
  } = useMultipleQueries([
    useOrderById(activityData?.order?.id, {
      enabled: !!activityData?.order?.id,
    }),
  ] as const)

  const {
    followUpDatetime,
    followUpMethod,
    updatedAt,
    customer,
    user,
    channel,
    status,
    lead,
    brands,
    createdAt,
    estimatedValue,
    feedback,
    activityBrandValues,
    reminderDateTime,
    reminderNote,
  } = activityData || {}
  const activityComment: ActivityComment[] = dataFromPaginated(
    activityCommentPaginatedData,
  )
  if (activityIsError || orderIsError) {
    return <Error refreshing={activityIsFetching} onRefresh={activityRefetch} />
  }

  if (activityIsLoading || (!!activityData?.order?.id && orderIsLoading)) {
    return <Loading />
  }
  const Header = (
    <>
      <Div bg="white" pt={30}>
        <Text fontSize={14} fontWeight="bold" mb={20} px={20}>
          Recent Activity
        </Text>
        <InfoBlock
          title="Orlan Number"
          data={activityData?.order?.orlanNumber}
        />
        <InfoBlock title="Created" data={formatDate(followUpDatetime)} />
        <InfoBlock title="Last Update" data={formatDate(updatedAt)} />
        <InfoBlock title="Activity Type" data={Case.title(followUpMethod)} />
        <InfoBlock title="Customer" data={`${getFullName(customer)}`} />
        <InfoBlock title="Sales" data={`${user.name}`} />
        <InfoBlock title="Channel" data={channel.name} />
        <InfoBlock
          title="Status"
          data={
            !!status && (
              <Tag
                rounded={20}
                containerColor={activityStatusConfig[status].bg}
                textColor={activityStatusConfig[status].textColor}
              >
                {activityStatusConfig[status].displayText}
              </Tag>
            )
          }
        />
        <InfoBlock
          title="Priority"
          data={
            <Div py={5} px={10} bg={leadStatusConfig[lead.status].bg}>
              <Text
                textAlign="center"
                color={leadStatusConfig[lead.status].textColor}
              >
                {leadStatusConfig[lead.status].displayText}
              </Text>
            </Div>
          }
        />

        {!!activityData.activityBrandValues &&
          activityData.activityBrandValues.length > 0 && (
            <Div
              pt={20}
              pb={15}
              px={20}
              borderBottomWidth={0.8}
              borderBottomColor="grey"
            >
              <Text mb={10}>Estimated Brands</Text>
              <Div py={5}>
                {activityData.activityBrandValues.map((brand) => (
                  <Div flexDir="row" alignItems="center" mb={10}>
                    <Image
                      width={40}
                      scalable
                      source={{
                        uri:
                          brand?.images?.length > 0
                            ? brand.images[0].url
                            : null,
                      }}
                    />
                    <Div
                      row
                      w={widthPercentageToDP(80)}
                      justifyContent="space-between"
                    >
                      <Text ml={5} textAlign="left">
                        {brand?.name}
                      </Text>
                      <Text ml={5} color="#c4c4c4" textAlign="right">
                        {formatCurrency(brand?.estimated_value)}
                      </Text>
                    </Div>
                  </Div>
                ))}
              </Div>
            </Div>
          )}
        {!!estimatedValue && (
          <InfoBlock
            title="Estimated Value"
            data={formatCurrency(estimatedValue)}
            mb={20}
          />
        )}

        {!!orderData && (
          <Div mb={20} borderBottomWidth={0.8} borderBottomColor="grey">
            <OrderDetail
              orderData={orderData}
              isDeals={isDeals}
              refetch={refetch}
            />
            {/* <OrderDemand orderData={orderData} isDeals={isDeals}/> */}
          </Div>
        )}

        <Div mb={20} px={20}>
          <Text mb={10}>Feedback</Text>
          <Div p={10} borderColor={COLOR_DISABLED} borderWidth={0.8}>
            <Text>{feedback || "-"}</Text>
          </Div>
        </Div>

        {!!reminderDateTime && (
          <>
            <InfoBlock
              title="Reminder"
              data={formatDate(reminderDateTime)}
              borderBottomWidth={0}
              pt={0}
              pb={10}
            />
            <Div
              mx={20}
              mb={20}
              p={10}
              borderColor={COLOR_DISABLED}
              borderWidth={0.8}
            >
              <Text>{reminderNote || "-"}</Text>
            </Div>
          </>
        )}
      </Div>

      <Div mt={10} bg="white" px={20} pt={30} pb={20}>
        <Text fontSize={14} fontWeight="bold">
          Activity Comments
        </Text>
      </Div>
    </>
  )

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <FlatList
        refreshControl={
          <RefreshControl
            colors={[COLOR_PRIMARY]}
            tintColor={COLOR_PRIMARY}
            titleColor={COLOR_PRIMARY}
            title="Loading..."
            refreshing={activityIsFetching}
            onRefresh={activityRefetch}
          />
        }
        data={activityComment}
        keyExtractor={({ id }) => `comment${id}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListEmptyComponent={() => (
          <Text fontSize={14} textAlign="center" p={20}>
            Kosong
          </Text>
        )}
        ListHeaderComponent={Header}
        onEndReachedThreshold={0.2}
        onEndReached={() => {
          if (activityHasNextPage) activityFetchNextPage()
        }}
        ListFooterComponent={
          <Div pb={20} bg="white">
            {!!activityComment &&
              activityComment.length > 0 &&
              (activityIsFetchingNextPage ? <FooterLoading /> : null)}
          </Div>
        }
        renderItem={({ item, index }) => <ActivityCommentItem comment={item} />}
      />
      <MessageForm
        onSubmit={async (data, { resetForm }) => {
          Keyboard.dismiss()
          await createComment({
            activityId: activityId,
            activityCommentId: null,
            content: data.chat,
          })
          resetForm()
        }}
      />
    </CustomKeyboardAvoidingView>
  )
}
