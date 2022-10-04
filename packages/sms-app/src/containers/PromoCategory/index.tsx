import { useNavigation } from "@react-navigation/native"
import React, { useRef } from "react"
import {
  Text,
  FlatList,
  View,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
} from "react-native"
import * as Animatable from "react-native-animatable"
import { Div } from "react-native-magnus"
import { heightPercentageToDP } from "react-native-responsive-screen"

import WYSIWYG from "components/WYSIWYG"

import useMultipleQueries from "hooks/useMultipleQueries"

import usePromoCategory from "api/hooks/promo/usePromoCategory"

import { dataFromPaginated } from "helper/pagination"

const PromoCategory = () => {
  const {
    queries: [{ data: promoPaginatedData }],
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      manualRefetch,
      isManualRefetching,
      isFetchingNextPage,
      hasNextPage,
      fetchNextPage,
    },
  } = useMultipleQueries([usePromoCategory()] as const)
  const categoryPromo = dataFromPaginated(promoPaginatedData)
  const navigation = useNavigation()
  const viewRef = useRef(null)
  const animation = Animations[Math.floor(Math.random() * Animations.length)]
  const ListItem = ({ item, index, animation, navigation }) => {
    return (
      <TouchableOpacity
        onPress={() => navigation.navigate("Promo", { id: item?.id })}
        activeOpacity={0.7}
      >
        <Animatable.View
          animation={animation}
          duration={1000}
          delay={index * 300}
        >
          <View style={styles.listItem}>
            <View style={styles.detailsContainer}>
              <Text numberOfLines={1} style={styles.name}>
                {item?.name}
              </Text>
              {/* <WYSIWYG body={item?.description} /> */}
            </View>
            <Image
              source={{ uri: item?.images[0]?.url }}
              style={styles.images}
            />
          </View>
        </Animatable.View>
      </TouchableOpacity>
    )
  }
  const renderItem = ({ item, index }) => (
    <ListItem
      item={item}
      index={index}
      animation={animation}
      navigation={navigation}
    />
  )
  return (
    <Div bg="white" flex={1} alignItems="center">
      <Animatable.View
        ref={viewRef}
        easing={"ease-in-out"}
        duration={500}
        style={{ flex: 1, marginTop: heightPercentageToDP(3) }}
      >
        <FlatList
          data={categoryPromo}
          keyExtractor={(_, i) => String(i)}
          numColumns={2}
          renderItem={renderItem}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={{ paddingBottom: 100 }}
        />
      </Animatable.View>
    </Div>
  )
}

export default PromoCategory

const styles = StyleSheet.create({
  name: {
    fontWeight: "bold",
    fontSize: 16,
    color: "white",
  },
  separator: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: "rgba(0, 0, 0, .08)",
  },
  listEmpty: {
    height: Dimensions.get("window").height,
    alignItems: "center",
    justifyContent: "center",
  },
  listItem: {
    height: 200,
    width: Dimensions.get("window").width / 2 - 16,
    backgroundColor: "grey",
    margin: 8,
    borderRadius: 10,
  },
  images: {
    height: 180,
    width: Dimensions.get("window").width / 2 - 16,
    borderBottomLeftRadius: 10,
    borderBottomRightRadius: 10,
  },
  image: {
    height: 150,
    margin: 5,
    borderRadius: 10,
    backgroundColor: "yellow",
  },
  detailsContainer: {
    paddingHorizontal: 16,
    borderTopLeftRadius: 10,
    borderTopRightRadius: 10,
    paddingVertical: 5,
    alignItems: "center",
    backgroundColor: "#313132",
  },
})
const Animations = [
  // "fadeIn",
  // "fadeInUp",
  // "fadeInDown",
  // "fadeInDownBig",
  // "fadeInUpBig",
  // "fadeInLeft",
  // "fadeInLeftBig",
  // "fadeInRight",
  // "fadeInRightBig",

  // "flipInX",
  // "flipInY",

  // "slideInDown",
  // "slideInUp",
  // "slideInLeft",
  // "slideInRight",

  "zoomIn",
  // "zoomInDown",
  // "zoomInUp",
  // "zoomInLeft",
  // "zoomInRight",
]
