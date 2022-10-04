import { useNavigation, useRoute } from "@react-navigation/native"
import moment from "moment"
import React, { useRef, useState } from "react"
import {
  Dimensions,
  FlatList,
  ScrollView,
  TouchableOpacity,
} from "react-native"
import {
  Button,
  Div,
  Dropdown,
  Icon,
  Input,
  ScrollDiv,
  Text,
} from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import useNewLeads from "api/hooks/target/useNewLeads"

import { responsive } from "helper"

const SalesNewLeads = () => {
  const dropdownRef = React.createRef()
  const navigation = useNavigation()
  const route = useRoute()
  const [status, setStatus] = useState<string>()
  const [name, setName] = useState("")
  const {
    queries: [{ data: leadData }],
    meta: { isLoading },
  } = useMultipleQueries([
    useNewLeads({
      user_type: route.params.type,
      id: route.params.id,
      is_active: route.params.isActive,
      start_date: moment(route.params.startDate).format("YYYY-MM-DD"),
      end_date: moment(route.params.endDate).format("YYYY-MM-DD"),
      status: status,
      name: name,
      perPage: 200,
    }),
  ] as const)
  const TopSection = () => {
    return (
      <Div
        row
        justifyContent="space-between"
        p={20}
        bg="primary"
        mb={10}
        alignItems="center"
      >
        <Div>
          <Text
            allowFontScaling={false}
            color="white"
            fontWeight="bold"
            fontSize={responsive(14)}
          >
            {route.params.name}
          </Text>
        </Div>

        <Button
          h={heightPercentageToDP(5)}
          w={widthPercentageToDP(25)}
          fontSize={responsive(9)}
          borderColor="#000"
          borderWidth={1}
          suffix={
            <Icon
              name="down"
              fontFamily="AntDesign"
              fontSize={12}
              ml={heightPercentageToDP(1)}
            />
          }
          bg="#fff"
          color="#000"
          onPress={() => dropdownRef.current.open()}
        >
          {!!status ? status : "Status"}
        </Button>
        <Dropdown
          ref={dropdownRef}
          title={
            <Text fontSize={responsive(10)} mx="xl" color="gray500" pb="md">
              Select filter status
            </Text>
          }
          mt="md"
          pb="2xl"
          showSwipeIndicator={true}
          roundedTop="xl"
        >
          <Dropdown.Option
            onPress={() => setStatus("HOT")}
            py="md"
            px="xl"
            block
          >
            HOT
          </Dropdown.Option>
          <Dropdown.Option
            onPress={() => setStatus("WARM")}
            py="md"
            px="xl"
            block
          >
            WARM
          </Dropdown.Option>
          <Dropdown.Option
            py="md"
            onPress={() => setStatus("COLD")}
            px="xl"
            block
          >
            COLD
          </Dropdown.Option>
          <Dropdown.Option
            py="md"
            onPress={() => setStatus("NONE")}
            px="xl"
            block
          >
            NO ACTIVITY
          </Dropdown.Option>
        </Dropdown>
      </Div>
    )
  }

  const renderItem = ({ item }) => {
    return (
      <TouchableOpacity
        onPress={() =>
          navigation.navigate("CustomerDetail", { leadId: item.id })
        }
      >
        <Div m={5} rounded={4} bg="#fff" overflow="hidden">
          <Div row justifyContent="space-between">
            <Div p={10}>
              <Text
                allowFontScaling={false}
                mb={10}
                fontSize={responsive(8)}
                color="#C4C4C4"
              >
                {item?.created_at}
              </Text>
              <Text
                allowFontScaling={false}
                fontWeight="bold"
                fontSize={responsive(10)}
              >
                {item?.customer}
              </Text>
              <Text allowFontScaling={false} fontSize={responsive(8)} my={5}>
                {item?.phone}
              </Text>
              <Text allowFontScaling={false} fontSize={responsive(8)}>
                {item?.email}
              </Text>
              <Div row mt={10}>
                <Icon
                  color="grey"
                  name="person"
                  fontSize={12}
                  fontFamily="Ionicons"
                  mr={5}
                />
                <Text>{item?.sales}</Text>
              </Div>
            </Div>

            <Div justifyContent="space-between" alignItems="flex-end">
              <Text
                allowFontScaling={false}
                color="#fff"
                fontWeight="bold"
                fontSize={responsive(9)}
                h={heightPercentageToDP(2.5)}
                w={widthPercentageToDP(20)}
                textAlign="center"
                bg={
                  item?.latest_activity?.latest_status === "HOT"
                    ? "#E53935"
                    : item?.latest_activity?.latest_status === "WARM"
                    ? "#FFD13D"
                    : item?.latest_activity?.latest_status === "COLD"
                    ? "#0553B7"
                    : "#c4c4c4"
                }
              >
                {!!item?.latest_activity?.latest_status
                  ? item?.latest_activity?.latest_status
                  : "No Activity"}
              </Text>
              <Text
                mb={10}
                textAlign="right"
                mr={5}
                color="#17949D"
                fontWeight="bold"
                fontSize={responsive(10)}
              >
                {item?.channel}
              </Text>
            </Div>
          </Div>
        </Div>
      </TouchableOpacity>
    )
  }
  return (
    <Div flex={1}>
      <TopSection />
      <Input
        mx={5}
        mb={10}
        placeholder="Search name lead"
        value={name}
        onChangeText={(val) => setName(val)}
        suffix={
          <Icon
            name="search"
            fontSize={responsive(12)}
            color="gray900"
            fontFamily="Feather"
          />
        }
      />
      {!!isLoading ? (
        <Loading />
      ) : (
        <FlatList data={leadData?.data} renderItem={renderItem} />
      )}
    </Div>
  )
}

export default SalesNewLeads
