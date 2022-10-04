import { useNavigation } from "@react-navigation/native"
import React from "react"
import {
  FlatList,
  ScrollView,
  Dimensions,
  TouchableOpacity,
  Image,
} from "react-native"
import { PageControlJaloro } from "react-native-chi-page-control"
import { Div } from "react-native-magnus"

import Text from "components/Text"

const BotSection = ({ data, startDate, endDate, userData }) => {
  const navigation = useNavigation()
  const windowWidth = Dimensions.get("window").width
  const renderTopSales = ({ item }) => (
    <>
      <Div
        py={14}
        bg="white"
        row
        borderBottomWidth={1}
        borderColor="#c4c4c4"
        rounded={0}
      >
        <Div flex={1.5}>
          <Text fontWeight="normal" textAlign="center">
            {item.priority}
          </Text>
        </Div>
        <Div flex={4}>
          <Text fontWeight="normal" textAlign="center">
            {item.model.name}
          </Text>
        </Div>
        <Div flex={3}>
          <Text fontWeight="normal" textAlign="center">
            {item.percentage}
          </Text>
        </Div>
      </Div>
    </>
  )
  const header = (title: string) => {
    return (
      <Div
        py={18}
        row
        bg="#17949D"
        style={{ borderTopLeftRadius: 10, borderTopRightRadius: 10 }}
      >
        <Div flex={1.5}>
          <Text color="white" fontWeight="bold" textAlign="center">
            No.
          </Text>
        </Div>
        <Div flex={4}>
          <Text color="white" fontWeight="bold" textAlign="center">
            {title}
          </Text>
        </Div>
        <Div flex={3}>
          <Text color="white" fontWeight="bold" textAlign="center">
            Achievement
          </Text>
        </Div>
      </Div>
    )
  }

  const [index, setIndex] = React.useState(0)
  const onScrollEnd = (e: any) => {
    let contentOffset = e.nativeEvent.contentOffset
    let viewSize = e.nativeEvent.layoutMeasurement
    let pageNum = contentOffset.x / viewSize.width
    setIndex(pageNum)
  }

  return (
    <>
      <Div row alignItems="center" px={20}>
        <Image
          source={require("../../assets/topSalesLogo.png")}
          style={{ height: 22, width: 22 }}
        />
        <Text fontSize={14} ml={8} fontWeight="bold">
          Top Performance
        </Text>
      </Div>

      <ScrollView
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        scrollEventThrottle={16}
        bounces={false}
        onMomentumScrollEnd={onScrollEnd}
      >
        {userData.type === "DIRECTOR" ? (
          <>
            <TouchableOpacity
              onPress={() =>
                navigation.navigate("TopSales", {
                  startDate: startDate,
                  endDate: endDate,
                  type: "supervisor",
                })
              }
            >
              <FlatList
                style={{ padding: 20, width: windowWidth }}
                renderItem={renderTopSales}
                data={data?.bum}
                keyExtractor={(_, idx: number) => idx.toString()}
                ListHeaderComponent={header("BUM Name")}
              />
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() =>
                navigation.navigate("TopSales", {
                  startDate: startDate,
                  endDate: endDate,
                  type: "channel",
                })
              }
            >
              <FlatList
                style={{ padding: 20, width: windowWidth }}
                renderItem={renderTopSales}
                data={data?.channel}
                keyExtractor={(_, idx: number) => idx.toString()}
                ListHeaderComponent={header("Channel Name")}
              />
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() =>
                navigation.navigate("TopSales", {
                  startDate: startDate,
                  endDate: endDate,
                  type: "sales",
                })
              }
            >
              <FlatList
                style={{ padding: 20, width: windowWidth }}
                renderItem={renderTopSales}
                data={data?.sales}
                keyExtractor={(_, idx: number) => idx.toString()}
                ListHeaderComponent={header("Sales Name")}
              />
            </TouchableOpacity>
          </>
        ) : userData.type === "SUPERVISOR" ? (
          <>
            <TouchableOpacity
              onPress={() =>
                navigation.navigate("TopSales", {
                  startDate: startDate,
                  endDate: endDate,
                  type: "channel",
                })
              }
            >
              <FlatList
                style={{ padding: 20, width: windowWidth }}
                renderItem={renderTopSales}
                data={data?.channel}
                keyExtractor={(_, idx: number) => idx.toString()}
                ListHeaderComponent={header("Channel Name")}
              />
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() =>
                navigation.navigate("TopSales", {
                  startDate: startDate,
                  endDate: endDate,
                  type: "sales",
                })
              }
            >
              <FlatList
                style={{ padding: 20, width: windowWidth }}
                renderItem={renderTopSales}
                data={data?.sales}
                keyExtractor={(_, idx: number) => idx.toString()}
                ListHeaderComponent={header("Sales Name")}
              />
            </TouchableOpacity>
          </>
        ) : (
          <TouchableOpacity
            onPress={() =>
              navigation.navigate("TopSales", {
                startDate: startDate,
                endDate: endDate,
                type: "sales",
              })
            }
          >
            <FlatList
              style={{ padding: 20, width: windowWidth }}
              renderItem={renderTopSales}
              data={data?.sales}
              keyExtractor={(_, idx: number) => idx.toString()}
              ListHeaderComponent={header("Sales Name")}
            />
          </TouchableOpacity>
        )}
      </ScrollView>
      {userData.type === "DIRECTOR" ? (
        <PageControlJaloro
          style={{ alignSelf: "center", marginBottom: 10 }}
          progress={index === 0 ? index : index === 1 ? 0.5 : 1}
          numberOfPages={3}
        />
      ) : userData.type === "SUPERVISOR" ? (
        <PageControlJaloro
          style={{ alignSelf: "center", marginBottom: 10 }}
          progress={index}
          numberOfPages={2}
        />
      ) : null}
    </>
  )
}

export default BotSection
