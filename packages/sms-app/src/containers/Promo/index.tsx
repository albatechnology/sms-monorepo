import { useRoute } from "@react-navigation/native"
import React from "react"
import { FlatList, RefreshControl } from "react-native"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import usePromoList from "api/hooks/promo/usePromoList"

import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_PRIMARY } from "helper/theme"

import PromoCard from "./PromoCard"

export default () => {
  const route = useRoute()
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
  } = useMultipleQueries([
    usePromoList({
      filterPromoCategoryId: route?.params?.id,
    }),
  ] as const)

  const promoList = dataFromPaginated(promoPaginatedData)

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <FlatList
      contentContainerStyle={[s.pX20, s.pY30, s.bgWhite]}
      data={promoList}
      keyExtractor={({ id }) => `promo_${id}`}
      refreshControl={
        <RefreshControl
          colors={[COLOR_PRIMARY]}
          tintColor={COLOR_PRIMARY}
          titleColor={COLOR_PRIMARY}
          title="Loading..."
          refreshing={isManualRefetching}
          onRefresh={manualRefetch}
        />
      }
      showsVerticalScrollIndicator={false}
      bounces={false}
      onEndReachedThreshold={0.2}
      onEndReached={() => {
        if (hasNextPage) fetchNextPage()
      }}
      ListFooterComponent={() =>
        !!promoList &&
        promoList.length > 0 &&
        (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
      }
      renderItem={({ item: promo, index }) => (
        <PromoCard key={`promo_${index}`} promo={promo} />
      )}
    />
  )
}
