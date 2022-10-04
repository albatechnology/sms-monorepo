import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  RouteProp,
  useRoute,
  useNavigation,
  CompositeNavigationProp,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { FlatList, Keyboard } from "react-native"
import { Avatar, Div } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import Error from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import MessageForm from "forms/MessageForm"

import useQAMessageCreateMutation from "api/hooks/qaMessage/useQAMessageCreateMutation"
import useQAMessageList from "api/hooks/qaMessage/useQAMessageList"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { ChatStackParamList, MainTabParamList } from "Router/MainTabParamList"

import { responsive } from "helper"
import Languages from "helper/languages"
import { dataFromPaginated } from "helper/pagination"
import { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

import { QAMessage } from "types/QAMessage"
import { User } from "types/User"

type CurrentScreenRouteProp = RouteProp<ChatStackParamList, "ChatDetail">

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<ChatStackParamList, "ChatDetail">,
  BottomTabNavigationProp<MainTabParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [createMessage] = useQAMessageCreateMutation()

  const chatId = route?.params?.id ?? -1
  if (chatId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Dashboard")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: qaMessagePaginatedData }, { data: userData }],
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      isFetchingNextPage,
      hasNextPage,
      fetchNextPage,
    },
  } = useMultipleQueries([
    useQAMessageList({ topic: chatId }),
    useUserLoggedInData(),
  ] as const)

  const data: QAMessage[] = dataFromPaginated(qaMessagePaginatedData)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <FlatList
        inverted
        data={data}
        keyExtractor={({ id }) => `chat${id}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        onEndReachedThreshold={0.2}
        onEndReached={() => {
          if (hasNextPage) fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        ListHeaderComponent={
          <MessageForm
            onSubmit={async (data, { resetForm }) => {
              Keyboard.dismiss()
              await createMessage({
                topicId: chatId,
                content: data.chat,
              })
              resetForm()
            }}
          />
        }
        renderItem={({ item, index }) => (
          <ChatCard item={item} userData={userData} />
        )}
      />
    </CustomKeyboardAvoidingView>
  )
}

type PropTypes = {
  item: QAMessage
  userData: User
}

const ChatCard = ({ item, userData }: PropTypes) => {
  if (item.sender.id === userData.id) {
    return (
      <Div p={10} row justifyContent="flex-end">
        <Div maxW={"70%"}>
          <Text fontWeight="bold" mb={5} textAlign="right">
            {item.sender.name}
          </Text>
          <Div p={10} bg={COLOR_DISABLED} rounded={4}>
            <Text>{item.content}</Text>
          </Div>
        </Div>
        <Avatar
          bg={COLOR_DISABLED}
          color={COLOR_PRIMARY}
          size={responsive(40)}
          ml={10}
        >
          {`${item.sender.name[0]}`}
        </Avatar>
      </Div>
    )
  } else {
    return (
      <Div p={10} row>
        <Avatar
          bg={COLOR_DISABLED}
          color={COLOR_PRIMARY}
          size={responsive(40)}
          mr={10}
        >
          {`${item.sender.name[0]}`}
        </Avatar>
        <Div maxW={"70%"}>
          <Text fontWeight="bold" mb={5}>
            {item.sender.name}
          </Text>
          <Div p={10} bg={COLOR_DISABLED} rounded={4}>
            <Text>{item.content}</Text>
          </Div>
        </Div>
      </Div>
    )
  }
}
