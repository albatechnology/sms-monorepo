import React, { useState } from "react"
import { FlatList, RefreshControl } from "react-native"
import { Button, Div, Text } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import NotFound from "components/CommonList/NotFound"
import Error from "components/Error"
import Loading from "components/Loading"

import useMultipleQueries from "hooks/useMultipleQueries"

import LeadFilter, { LeadFilterType } from "filters/LeadFilter"

import { LeadType } from "api/generated/enums"
import useLeadsListByUnhandled from "api/hooks/lead/useLeadsListByUnhandled"
import useLeadsListByUser from "api/hooks/lead/useLeadsListByUser"

import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_PRIMARY } from "helper/theme"

import { Lead } from "types/Lead"
import { filterEnum } from "types/helper"

import LeadCard from "./LeadCard"

type PropTypes = {
  isUnhandled?: boolean
  type: LeadType
  isDirector?: boolean
}

export default ({ isUnhandled, type, isDirector }: PropTypes) => {
  const [filters, setFilter] = useState<LeadFilterType>({})
  const [sort, setSort] = useState("")
  const [toggleDrop, setToggleDrop] = useState(type)
  const [ascendingSort, setAscendingSort] = useState(true)

  const { filterUserId, ...restOfFilters } = filters
  const processedFilter = {
    ...restOfFilters,
    ...(filterUserId
      ? {
          filterUserId: filterEnum(filterUserId),
        }
      : {}),
  }

  const query = isUnhandled
    ? useLeadsListByUnhandled(processedFilter)
    : useLeadsListByUser({
        filterType: toggleDrop,
        ...processedFilter,
        sort: sort ? (ascendingSort ? sort : `-${sort}`) : "",
      })
  const {
    queries: [{ data: leadPaginatedData }],
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
  } = useMultipleQueries([query] as const)
  const data: Lead[] = dataFromPaginated(leadPaginatedData)
  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }
  return (
    <>
      <LeadFilter
        activeFilterValues={filters}
        activeSort={sort}
        onSetFilter={setFilter}
        onSetSort={setSort}
        ascendingSort={ascendingSort}
        setAscendingSort={setAscendingSort}
      />
      {isLoading ? (
        <Loading />
      ) : (
        <>
          <FlatList
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
            contentContainerStyle={[
              { flexGrow: 1, backgroundColor: "#f9f7f7" },
            ]}
            data={data}
            keyExtractor={({ id }) => `customer_${id}`}
            showsVerticalScrollIndicator={false}
            ListEmptyComponent={() => <NotFound />}
            onEndReachedThreshold={0.2}
            onEndReached={() => {
              if (hasNextPage) fetchNextPage()
            }}
            ListHeaderComponent={() => (
              <>
                {isUnhandled === true ? (
                  <Div bg="#17949D" justifyContent="center" mb={10}>
                    <Text
                      ml={10}
                      color="white"
                      textAlign="center"
                      fontWeight="bold"
                    >
                      {query.data.pages[0].meta.total} Unassigned Leads
                    </Text>
                  </Div>
                ) : isDirector === true ? (
                  <Div row justifyContent="center" mb={10} mx={0}>
                    <Button
                      onPress={() => setToggleDrop("DEAL")}
                      flex={0.5}
                      rounded={0}
                      bg={toggleDrop === "DEAL" ? "primary" : "white"}
                      color={toggleDrop === "DEAL" ? "white" : "primary"}
                    >
                      Deals
                    </Button>
                    <Button
                      onPress={() => setToggleDrop("DROP")}
                      bg={toggleDrop === "DROP" ? "primary" : "white"}
                      color={toggleDrop === "DROP" ? "white" : "primary"}
                      flex={0.5}
                      rounded={0}
                    >
                      Drop
                    </Button>
                  </Div>
                ) : (
                  <Div h={10} />
                )}
              </>
            )}
            ListFooterComponent={() =>
              !!data &&
              data.length > 0 &&
              (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
            }
            renderItem={({ item: lead, index }) => (
              <LeadCard isUnhandled={isUnhandled} lead={lead} />
            )}
          />
        </>
      )}
    </>
  )
}
