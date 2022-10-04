import { useNavigation } from "@react-navigation/native"
import moment from "moment"
import React, { useState } from "react"
import {
  FlatList,
  Pressable,
  RefreshControl,
  TouchableHighlight,
  TouchableOpacity,
} from "react-native"
import {
  Button,
  Div,
  Icon,
  Input,
  Modal,
  ScrollDiv,
  Text,
  Tooltip,
} from "react-native-magnus"
import * as Progress from "react-native-progress"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import DatePickerInput from "components/DatePickerInput"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import useTarget from "api/hooks/target/useTarget"
import useTargetDetail from "api/hooks/target/useTargetDetail"

import { formatCurrency, responsive } from "helper"
import { COLOR_PRIMARY } from "helper/theme"

type PropTypes = {
  type: string
}
const TargetList = ({ type }: PropTypes) => {
  const [visible, setVisible] = useState(false)
  const [visibleActivity, setVisibleActiviy] = useState(false)
  const [visibleCompare, setVisibleCompare] = useState(false)
  const [index, setIndex] = useState<any>()
  const [date, setDate] = useState<any>()
  const [key, setKey] = useState<string>()
  const [start, setStart] = useState<any>()
  const [end, setEnd] = useState<any>()
  const [search, setSearch] = useState<string>()
  const navigation = useNavigation()
  const {
    queries: [{ data: targetData }],
    meta: { isLoading, isFetching, refetch },
  } = useMultipleQueries([
    useTargetDetail({
      user_type: type,
      start_date: !!start ? moment(start).format("YYYY-MM-DD") : "",
      end_date: !!end ? moment(end).format("YYYY-MM-DD") : "",
      name: search,
    }),
  ] as const)
  const [show, setShow] = useState(69)
  const renderStatus = ({ data }) => (
    <Pressable
      onPress={() =>
        navigation.navigate("FollowTarget", {
          type: type,
          id: item?.id,
          name: item?.name,
        })
      }
    >
      <Div
        alignItems="center"
        row
        h={heightPercentageToDP(5)}
        justifyContent="space-between"
        borderTopWidth={1}
        borderColor="#D9D9D9"
      >
        <Div row justifyContent="center" alignItems="center">
          <Div mx={8} h={8} w={8} rounded={8 / 2} bg={data.color} />
          <Text>{data.status}</Text>
        </Div>
        <Text>{data.total}</Text>
      </Div>
    </Pressable>
  )
  const Filter = () => {
    return (
      <Div>
        <Pressable onPress={() => setVisible(!visible)}>
          <Icon
            ml={heightPercentageToDP(2)}
            alignSelf="center"
            name="filter"
            fontFamily="Ionicons"
            fontSize={32}
          />
        </Pressable>
      </Div>
    )
  }
  const renderItem = ({ item, index }) => {
    const status = [
      {
        status: "Hot",
        total: item?.follow_up?.hot_activities,
        color: "#F44336",
      },
      {
        status: "Warm",
        total: item?.follow_up?.warm_activities,
        color: "#FFD13D",
      },
      {
        status: "Cold",
        total: item?.follow_up?.cold_activities,
        color: "#0553B7",
      },
    ]
    return (
      <Div p={10} bg="white" my={10} rounded={6}>
        <Div
          row
          justifyContent="space-between"
          borderBottomWidth={1}
          borderColor="#c4c4c4"
        >
          <Text
            allowFontScaling={false}
            mb={10}
            fontWeight="bold"
            fontSize={responsive(12)}
          >
            {item.name}
          </Text>
          <Div row alignItems="center">
            <TouchableOpacity
              style={{ width: widthPercentageToDP(10) }}
              onPress={() => {
                setVisibleCompare(true)
                setIndex(index)
              }}
            >
              <Icon
                name="git-compare-outline"
                fontFamily="Ionicons"
                fontSize={16}
                color="#313132"
              />
            </TouchableOpacity>
            <TouchableOpacity
              style={{ width: widthPercentageToDP(10) }}
              onPress={() => {
                show === index ? setShow(999) : setShow(index)
              }}
            >
              <Icon
                name={
                  show === index ? "keyboard-arrow-up" : "keyboard-arrow-down"
                }
                fontFamily="MaterialIcons"
                fontSize={30}
                color="#313132"
              />
            </TouchableOpacity>
          </Div>
        </Div>
        <Div row mt={heightPercentageToDP(0.5)} justifyContent="center" p={10}>
          <Pressable
            onPress={() =>
              navigation.navigate("QuotationInside", {
                type: type,
                id: item?.id,
                name: item?.name,
                invoice_type: "deals",
                startDate: !!start ? start : moment().startOf("month"),
                endDate: !!end ? end : moment().endOf("month"),
              })
            }
          >
            <Div
              w={widthPercentageToDP(32)}
              px={10}
              h={heightPercentageToDP(10)}
              bg="white"
            >
              <Text allowFontScaling={false} fontSize={10}>
                Deals
              </Text>
              <Div row mb={5}>
                <Text
                  allowFontScaling={false}
                  fontSize={responsive(8)}
                  fontWeight="bold"
                  color="#17949D"
                >
                  {formatCurrency(item?.deals?.value)}
                </Text>
                <Icon
                  ml={3}
                  name={
                    item?.deals?.value < item?.deals?.compare
                      ? "caretdown"
                      : "caretup"
                  }
                  fontFamily="AntDesign"
                  fontSize={8}
                  color={
                    item?.deals?.value < item?.deals?.compare
                      ? "#F44336"
                      : "#2DCC70"
                  }
                />
              </Div>
              <Progress.Bar
                borderRadius={0}
                progress={
                  item?.deals?.value / item?.deals?.target_deals === Infinity ||
                  isNaN(item?.deals?.value / item?.deals?.target_deals)
                    ? 0
                    : item?.deals?.value / item?.deals?.target_deals
                }
                color="#17949D"
                borderWidth={0}
                height={3}
                useNativeDriver
                unfilledColor="#c4c4c4"
                width={widthPercentageToDP(30)}
                style={{ marginBottom: 5 }}
              />

              <Text allowFontScaling={false} fontSize={responsive(8)}>
                Target {formatCurrency(item?.deals?.target_deals)}
              </Text>
            </Div>
          </Pressable>
          <Pressable
            onPress={() =>
              navigation.navigate("InteriorDesignInside", {
                type: type,
                id: item?.id,
                name: item?.name,
                startDate: !!start ? start : moment().startOf("month"),
                endDate: !!end ? end : moment().endOf("month"),
              })
            }
          >
            <Div
              w={widthPercentageToDP(30)}
              px={10}
              h={heightPercentageToDP(10)}
              bg="white"
            >
              <Text allowFontScaling={false} fontSize={10}>
                Interior Design
              </Text>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={responsive(8)}
                  fontWeight="bold"
                  color="#F7AD97"
                >
                  {formatCurrency(item?.interior_design?.value)}
                </Text>
              </Div>
              <Text allowFontScaling={false} fontSize={responsive(8)}>
                Deals {item?.interior_design?.total_transaction}
              </Text>
            </Div>
          </Pressable>
          <Pressable
            onPress={() =>
              navigation.navigate("QuotationInside", {
                type: type,
                id: item?.id,
                name: item?.name,
                invoice_type: "retail",
                startDate: !!start ? start : moment().startOf("month"),
                endDate: !!end ? end : moment().endOf("month"),
              })
            }
          >
            <Div
              w={widthPercentageToDP(30)}
              rounded={4}
              px={10}
              h={heightPercentageToDP(10)}
              bg="white"
            >
              <Text allowFontScaling={false} fontSize={10}>
                Retail
              </Text>
              <Div row>
                <Text
                  allowFontScaling={false}
                  fontSize={responsive(8)}
                  fontWeight="bold"
                  color="#F5907B"
                >
                  {formatCurrency(item?.retail?.value)}
                </Text>
              </Div>
              <Text allowFontScaling={false} fontSize={responsive(8)}>
                Deals {item?.retail?.total_transaction}
              </Text>
            </Div>
          </Pressable>
        </Div>
        {show === index ? (
          <Div mx={5}>
            {/* Follow Up */}
            <Div mt={0}>
              <Text allowFontScaling={false} fontSize={responsive(10)}>
                Follow Up
              </Text>

              <Div row alignItems="center">
                <Text
                  allowFontScaling={false}
                  fontSize={responsive(12)}
                  my={5}
                  fontWeight="bold"
                >
                  {item?.follow_up?.total_activities?.value}
                </Text>
                <Icon
                  ml={5}
                  name={
                    item?.follow_up?.total_activities?.value <
                    item?.follow_up?.total_activities?.compare
                      ? "caretdown"
                      : "caretup"
                  }
                  fontFamily="AntDesign"
                  fontSize={10}
                  color={
                    item?.follow_up?.total_activities?.value <
                    item?.follow_up?.total_activities?.compare
                      ? "#F44336"
                      : "#2DCC70"
                  }
                />
              </Div>
              <Progress.Bar
                borderRadius={0}
                progress={
                  item?.follow_up?.total_activities?.value /
                    item?.follow_up?.total_activities?.target_activities ===
                    Infinity ||
                  isNaN(
                    item?.follow_up?.total_activities?.value /
                      item?.follow_up?.total_activities?.target_activities,
                  )
                    ? 0
                    : item?.follow_up?.total_activities?.value /
                      item?.follow_up?.total_activities?.target_activities
                }
                color="#17949D"
                borderWidth={0}
                height={3}
                useNativeDriver
                unfilledColor="#c4c4c4"
                width={widthPercentageToDP("60%")}
              />
              <Text my={5} fontSize={responsive(8)} color="#c4c4c4">
                Target {item?.follow_up?.total_activities?.target_activities}{" "}
                {`(${Math.round(
                  (item?.new_leads?.value / item?.new_leads?.target_leads) *
                    100,
                )}%)`}
              </Text>
              <FlatList
                data={status}
                renderItem={({ item: data }) => (
                  <Pressable
                    onPress={() =>
                      navigation.navigate("FollowTarget", {
                        type: type,
                        id: item?.id,
                        name: item?.name,
                        status: data.status,
                        startDate: !!start ? start : moment().startOf("month"),
                        endDate: !!end ? end : moment().endOf("month"),
                      })
                    }
                  >
                    <Div
                      alignItems="center"
                      row
                      h={heightPercentageToDP(5)}
                      justifyContent="space-between"
                      borderTopWidth={1}
                      borderColor="#D9D9D9"
                    >
                      <Div row justifyContent="center" alignItems="center">
                        <Div
                          mx={8}
                          h={8}
                          w={8}
                          rounded={8 / 2}
                          bg={data.color}
                        />
                        <Text>{data.status}</Text>
                      </Div>
                      <Text>{data.total}</Text>
                    </Div>
                  </Pressable>
                )}
              />
            </Div>

            {/* Quotation n Deals */}
            <Div row mt={10} justifyContent="center">
              <Div>
                <Pressable
                  onPress={() =>
                    navigation.navigate("SalesNewLeads", {
                      type: type,
                      id: item?.id,
                      name: item?.name,
                      startDate: !!start ? start : moment().startOf("month"),
                      endDate: !!end ? end : moment().endOf("month"),
                      isActive: 0,
                    })
                  }
                >
                  <Div
                    w={widthPercentageToDP(28)}
                    rounded={4}
                    px={10}
                    h={heightPercentageToDP(7)}
                    bg="white"
                  >
                    <Text allowFontScaling={false} fontSize={10}>
                      New Leads
                    </Text>
                    <Div row mb={5}>
                      <Text
                        allowFontScaling={false}
                        fontSize={responsive(8)}
                        color="#17949D"
                        fontWeight="bold"
                      >
                        {item?.new_leads?.value}
                      </Text>
                      <Icon
                        ml={3}
                        name={
                          item?.new_leads?.value < item?.new_leads?.compare
                            ? "caretdown"
                            : "caretup"
                        }
                        fontFamily="AntDesign"
                        fontSize={8}
                        color={
                          item?.new_leads?.value < item?.new_leads?.compare
                            ? "#F44336"
                            : "#2DCC70"
                        }
                      />
                    </Div>
                    <Progress.Bar
                      borderRadius={0}
                      progress={
                        item?.new_leads?.value /
                          item?.new_leads?.target_leads ===
                          Infinity ||
                        isNaN(
                          item?.new_leads?.value /
                            item?.new_leads?.target_leads,
                        )
                          ? 0
                          : item?.new_leads?.value /
                            item?.new_leads?.target_leads
                      }
                      color="#17949D"
                      borderWidth={0}
                      height={3}
                      useNativeDriver
                      unfilledColor="#c4c4c4"
                      width={widthPercentageToDP(20)}
                      style={{ marginBottom: 5 }}
                    />
                    <Text color="#c4c4c4" fontSize={10}>
                      Target {item?.new_leads?.target_leads}{" "}
                      {`(${Math.round(
                        (item?.new_leads?.value /
                          item?.new_leads?.target_leads) *
                          100,
                      )}%)`}{" "}
                    </Text>
                  </Div>
                </Pressable>
                <Div
                  w={widthPercentageToDP(28)}
                  mt={5}
                  px={10}
                  h={heightPercentageToDP(3)}
                  alignItems="flex-start"
                >
                  <Pressable
                    onPress={() =>
                      navigation.navigate("SalesNewLeads", {
                        type: type,
                        id: item?.id,
                        name: item?.name,
                        startDate: !!start ? start : moment().startOf("month"),
                        endDate: !!end ? end : moment().endOf("month"),
                        isActive: 1,
                      })
                    }
                  >
                    <Div row>
                      <Text
                        allowFontScaling={false}
                        fontSize={responsive(8)}
                        mr={2}
                        color="#17949D"
                        fontWeight="bold"
                      >{`${item?.active_leads?.value} Active`}</Text>
                    </Div>
                  </Pressable>
                </Div>
              </Div>

              <Pressable
                onPress={() =>
                  navigation.navigate("QuotationInside", {
                    type: type,
                    id: item?.id,
                    name: item?.name,
                    invoice_type: "quotation",
                    startDate: !!start ? start : moment().startOf("month"),
                    endDate: !!end ? end : moment().endOf("month"),
                  })
                }
              >
                <Div
                  w={widthPercentageToDP(32)}
                  rounded={4}
                  px={15}
                  h={heightPercentageToDP(10)}
                  bg="white"
                >
                  <Text allowFontScaling={false} fontSize={10}>
                    Quotation
                  </Text>
                  <Div row>
                    <Text
                      allowFontScaling={false}
                      fontSize={responsive(8)}
                      fontWeight="bold"
                      color="#17949D"
                    >
                      {formatCurrency(item?.quotation?.value)}
                    </Text>
                    <Icon
                      ml={3}
                      name={
                        item?.quotation?.value < item?.quotation?.compare
                          ? "caretdown"
                          : "caretup"
                      }
                      fontFamily="AntDesign"
                      fontSize={8}
                      color={
                        item?.quotation?.value < item?.quotation?.compare
                          ? "#F44336"
                          : "#2DCC70"
                      }
                    />
                  </Div>
                </Div>
              </Pressable>

              <Pressable
                onPress={() =>
                  navigation.navigate("EstimatedInside", {
                    type: type,
                    id: item?.id,
                    company_id: item?.company,
                    name: item?.name,
                    startDate: !!start ? start : moment().startOf("month"),
                    endDate: !!end ? end : moment().endOf("month"),
                  })
                }
              >
                <Div
                  w={widthPercentageToDP(32)}
                  rounded={4}
                  px={5}
                  h={heightPercentageToDP(10)}
                  bg="white"
                >
                  <Text allowFontScaling={false} fontSize={10}>
                    Pipelines
                  </Text>
                  <Div row>
                    <Text
                      allowFontScaling={false}
                      fontSize={responsive(8)}
                      color="#17949D"
                      fontWeight="bold"
                    >
                      {formatCurrency(item?.estimation?.value)}
                    </Text>
                    <Icon
                      ml={3}
                      name={
                        item?.estimation?.value < item?.estimation?.compare
                          ? "caretdown"
                          : "caretup"
                      }
                      fontFamily="AntDesign"
                      fontSize={8}
                      color={
                        item?.estimation?.value < item?.estimation?.compare
                          ? "#F44336"
                          : "#2DCC70"
                      }
                    />
                  </Div>
                </Div>
              </Pressable>
            </Div>
          </Div>
        ) : null}
      </Div>
    )
  }

  const Comparison = () => (
    <Modal
      isVisible={visibleCompare}
      h={heightPercentageToDP(60)}
      roundedTop={6}
      onBackdropPress={() => setVisibleCompare(!visibleCompare)}
    >
      <Div row justifyContent="space-between" p={20}>
        <Text
          allowFontScaling={false}
          fontWeight="bold"
          fontSize={responsive(14)}
        >
          Comparison
        </Text>
        <Button
          bg="#c4c4c4"
          h={35}
          w={35}
          rounded="circle"
          onPress={() => {
            setVisibleCompare(!visibleCompare)
          }}
        >
          <Icon color="black" name="close" />
        </Button>
      </Div>
      <Div
        row
        justifyContent="space-around"
        alignItems="center"
        borderColor="#c4c4c4"
        borderBottomWidth={1}
      >
        <Div row flex={3}>
          <Text
            textAlign="left"
            ml={heightPercentageToDP(2)}
            allowFontScaling={false}
            fontSize={responsive(12)}
          >
            Detail
          </Text>
        </Div>
        <Div flex={3}>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(targetData?.data?.compare_date?.start).format(
              "DD MMM YYYY",
            )}
          </Text>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(targetData?.data?.compare_date?.end).format("DD MMM YYYY")}
          </Text>
        </Div>
        <Div flex={3}>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(targetData?.data?.original_date?.start).format(
              "DD MMM YYYY",
            )}
          </Text>
          <Text
            textAlign="right"
            mr={heightPercentageToDP(1)}
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            {moment(targetData?.data?.original_date?.end).format("DD MMM YYYY")}
          </Text>
        </Div>
      </Div>

      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Deals
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              targetData?.data?.details[index]?.deals?.value <
              targetData?.data?.details[index]?.deals?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              targetData?.data?.details[index]?.deals?.value <
              targetData?.data?.details[index]?.deals?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(targetData?.data?.details[index]?.deals?.compare)}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(targetData?.data?.details[index]?.deals?.value)}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            New Leads
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              targetData?.data?.details[index]?.new_leads?.value <
              targetData?.data?.details[index]?.new_leads?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              targetData?.data?.details[index]?.new_leads?.value <
              targetData?.data?.details[index]?.new_leads?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {targetData?.data?.details[index]?.new_leads?.compare}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {targetData?.data?.details[index]?.new_leads?.value}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Follow Up
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              targetData?.data?.details[index]?.follow_up?.total_activities
                ?.value <
              targetData?.data?.details[index]?.follow_up?.total_activities
                ?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              targetData?.data?.details[index]?.follow_up?.total_activities
                ?.value <
              targetData?.data?.details[index]?.follow_up?.total_activities
                ?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {
            targetData?.data?.details[index]?.follow_up?.total_activities
              ?.compare
          }
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {targetData?.data?.details[index]?.follow_up?.total_activities?.value}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Quotation
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              targetData?.data?.details[index]?.quotation?.value <
              targetData?.data?.details[index]?.quotation?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              targetData?.data?.details[index]?.quotation?.value <
              targetData?.data?.details[index]?.quotation?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(targetData?.data?.details[index]?.quotation?.compare)}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(targetData?.data?.details[index]?.quotation?.value)}
        </Text>
      </Div>
      <Div
        alignItems="center"
        h={heightPercentageToDP(5)}
        row
        justifyContent="space-around"
        borderColor="#c4c4c4"
        borderBottomWidth={0.5}
      >
        <Div row flex={3}>
          <Text
            ml={heightPercentageToDP(2)}
            textAlign="left"
            allowFontScaling={false}
            fontSize={responsive(10)}
          >
            Pipelines
          </Text>
          <Icon
            ml={5}
            mt={2}
            name={
              targetData?.data?.details[index]?.estimation?.value <
              targetData?.data?.details[index]?.estimation?.compare
                ? "caretdown"
                : "caretup"
            }
            fontFamily="AntDesign"
            fontSize={10}
            color={
              targetData?.data?.details[index]?.estimation?.value <
              targetData?.data?.details[index]?.estimation?.compare
                ? "#F44336"
                : "#2DCC70"
            }
          />
        </Div>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(
            targetData?.data?.details[index]?.estimation?.compare,
          )}
        </Text>
        <Text
          allowFontScaling={false}
          flex={3}
          textAlign="right"
          mr={heightPercentageToDP(1)}
          fontSize={responsive(10)}
        >
          {formatCurrency(targetData?.data?.details[index]?.estimation?.value)}
        </Text>
      </Div>
    </Modal>
  )

  if (isLoading) {
    return <Loading />
  }
  return (
    <Div>
      <Div row alignItems="center" justifyContent="center" mt={10}>
        <Input
          w={widthPercentageToDP(80)}
          placeholder={`Search ${type}`}
          value={key}
          returnKeyType="search"
          onChangeText={(val) => setKey(val)}
          onSubmitEditing={() => setSearch(key)}
          suffix={
            <Icon
              name="search"
              fontSize={responsive(12)}
              color="gray900"
              fontFamily="Feather"
            />
          }
        />
        <Filter />
      </Div>
      <FlatList
        refreshControl={
          <RefreshControl
            colors={[COLOR_PRIMARY]}
            tintColor={COLOR_PRIMARY}
            titleColor={COLOR_PRIMARY}
            title="Loading..."
            refreshing={isFetching}
            onRefresh={refetch}
          />
        }
        contentContainerStyle={{ padding: 10 }}
        style={{ marginBottom: heightPercentageToDP(5) }}
        data={targetData?.data?.details}
        keyExtractor={(_, idx: number) => idx.toString()}
        renderItem={renderItem}
      />

      <Modal
        isVisible={visible}
        h={heightPercentageToDP(35)}
        roundedTop={6}
        onBackdropPress={() => setVisible(!visible)}
      >
        <Text fontSize={responsive(10)} p={10}>
          Select filter date interval
        </Text>
        <Div m={10} row>
          <Div flex={1} mr={10}>
            <DatePickerInput
              placeholder="Start Date"
              value={date}
              reset={false}
              onSelect={(val) => setDate(val)}
            />
          </Div>
          <Div flex={1}>
            <DatePickerInput
              placeholder="End Date"
              value={end}
              reset={false}
              onSelect={(val) => {
                setStart(date)
                setEnd(val)
              }}
            />
          </Div>
        </Div>
      </Modal>
      <Comparison />
    </Div>
  )
}

export default TargetList
