import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React from "react"
import { View, FlatList, TouchableOpacity } from "react-native"
import { Div, Icon, Text } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"

import LeadCard from "containers/Costumer/LeadCard"

import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import useFollowTarget from "api/hooks/target/useFollowTarget"

import { formatCurrency, responsive } from "helper"

const FollowTarget = () => {
  const route = useRoute()
  const params = route.params
  const {
    queries: [{ data: FollowData }],
    meta: { isLoading },
  } = useMultipleQueries([
    useFollowTarget({
      user_type: params.type,
      id: params.id,
      start_date: moment(params.startDate).format('YYYY-MM-DD'),
      end_date: moment(params.endDate).format('YYYY-MM-DD'),
      perPage: 100,
      activity_status: params.status.toUpperCase(),
    }),
  ] as const)
  const TopSection = () => {
    return (
      <Div row justifyContent="space-between" bg="primary" p={20}>
        <Div>
          <Text
            allowFontScaling={false}
            fontWeight="bold"
            fontSize={responsive(14)}
            color="white"
          >
            {params?.name}
          </Text>
        </Div>
        <Div>
          <Text
            mr={heightPercentageToDP(2)}
            fontWeight="bold"
            fontSize={responsive(14)}
            color={
              params?.status === "Hot"
                ? "#E53935"
                : params?.status === "Warm"
                ? "#FFD13D"
                : params?.status === "Cold"
                ? "#0553B7"
                : null
            }
          >
            {params?.status}
          </Text>
        </Div>
      </Div>
    )
  }
  const navigation = useNavigation()
  const renderItem = ({ item }) => {
    return (
      <TouchableOpacity
        onPress={() =>
          navigation.navigate("CustomerDetail", { leadId: item.id })
        }
      >
        <Div m={10} rounded={8} bg="#fff" p={10}>
          <Div>
            <Div>
              <Text
                color="#C4C4C4"
                allowFontScaling={false}
                fontSize={responsive(9)}
              >
                {moment(item?.created_at).format("YYYY-MM-DD")}
              </Text>
            </Div>

            <Div>
              <Div>
                <Text allowFontScaling={false} fontSize={responsive(10)}>
                  {item?.customer?.first_name} {item?.customer?.last_name}
                </Text>
              </Div>
              <Text allowFontScaling={false} fontSize={responsive(8)}>
                {item?.customer?.phone}
              </Text>
              <Text
                mb={heightPercentageToDP(1)}
                allowFontScaling={false}
                fontSize={responsive(8)}
              >
                {item?.customer?.email}
              </Text>
              <Div justifyContent="space-between" row mt={10}>
                <Div row>
                  <Icon
                    color="grey"
                    name="person"
                    fontSize={12}
                    fontFamily="Ionicons"
                    mr={5}
                  />
                  <Text>{item?.user?.name}</Text>
                </Div>
                <Div>
                  <Text
                    textAlign="right"
                    mr={5}
                    color="#17949D"
                    fontWeight="bold"
                    fontSize={responsive(10)}
                  >
                    {item?.channel?.name}
                  </Text>
                </Div>
              </Div>
            </Div>
          </Div>
          <Div h={5} />
        </Div>
      </TouchableOpacity>
    )
  }
  if (isLoading) {
    return <Loading />
  }

  return (
    <Div flex={1}>
      <TopSection />
      <FlatList data={FollowData?.data} renderItem={renderItem} />
    </Div>
  )
}

export default FollowTarget
