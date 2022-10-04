import { RouteProp, useNavigation, useRoute } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useMemo } from "react"
import { FlatList, RefreshControl } from "react-native"
import { Div } from "react-native-magnus"

import { ReportInfo } from "containers/Dashboard/ReportInfo"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import ErrorComponent from "components/Error"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useReportTargetList from "api/hooks/reportTarget/useReportTargetList"
import useSupervisorTypeList from "api/hooks/supervisorType/useSupervisorTypeList"

import { EntryStackParamList } from "Router/EntryStackParamList"

import s, { COLOR_PRIMARY } from "helper/theme"

import { handleTargetDrilldown, processReportTarget } from "types/ReportTarget"

type CurrentScreenRouteProp = RouteProp<EntryStackParamList, "ReportDrillDown">
type CurrentScreenNavigationProp = StackNavigationProp<
  EntryStackParamList,
  "ReportDrillDown"
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const originalSerializedFilter = route?.params?.originalSerializedFilter
  const originalFilters = JSON.parse(originalSerializedFilter)

  const filterType = route?.params?.filterType
  const supervisorTypeId = route?.params?.supervisorTypeId
  const parentSupervisorId = route?.params?.parentSupervisorId
  const companyId = route?.params?.companyId

  const { data: supervisorTypeList, isLoading: supervisorTypeIsLoading } =
    useSupervisorTypeList()

  const currentSupervisorType = useMemo(() => {
    return supervisorTypeList?.find((x) => x.id === supervisorTypeId)
  }, [supervisorTypeId, supervisorTypeList])

  const sortedSupervisorTypeList = useMemo(() => {
    return supervisorTypeList
      ?.filter((x) => !!x.level)
      ?.sort((a, b) => (b.level ?? 0) - (a.level ?? 0))
  }, [supervisorTypeList])

  const newSupervisorType = useMemo(() => {
    if (supervisorTypeIsLoading) {
      return null
    }
    if (supervisorTypeId === null) {
      // If company, then we give the highest level
      return sortedSupervisorTypeList[0]
    }

    return sortedSupervisorTypeList?.find(
      (supervisorType) => supervisorType.level < currentSupervisorType?.level,
    )
  }, [
    supervisorTypeIsLoading,
    supervisorTypeId,
    sortedSupervisorTypeList,
    currentSupervisorType?.level,
  ])

  const {
    queries: [reportTargetsQuery],
    meta: {
      isError,
      isLoading,
      isFetching,
      refetch,
      isManualRefetching,
      manualRefetch,
      hasNextPage,
      fetchNextPage,
      isFetchingNextPage,
    },
  } = useMultipleQueries(
    [
      useReportTargetList(
        {
          ...originalFilters,
          filterReportableIds: "",
          filterReportableType: "USER",
          ...(companyId ? { filterCompanyId: companyId } : {}),
          ...(parentSupervisorId
            ? { filterDescendantOf: parentSupervisorId }
            : {}),
          ...(newSupervisorType
            ? { filterSupervisorTypeLevel: newSupervisorType.level }
            : { filterSupervisorTypeLevel: -1 }),
        },
        30,
        { enabled: !supervisorTypeIsLoading },
      ),
    ] as const,
    { useStandardIsLoadingBehaviour: true },
  )

  const rawReportTargets = useMemo(
    () => reportTargetsQuery.data ?? [],
    [reportTargetsQuery],
  )

  const data = useMemo(() => {
    return processReportTarget(rawReportTargets).filter(
      (target) => target.type === filterType,
    )
  }, [rawReportTargets, filterType])

  if (isError) {
    return <ErrorComponent refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading || supervisorTypeIsLoading) {
    return <Loading />
  }
  return (
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
        contentContainerStyle={[{ flexGrow: 1 }, s.p20, s.bgWhite]}
        data={data}
        keyExtractor={({ id }) => `drillDown${id}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListEmptyComponent={() => (
          <Text fontSize={14} textAlign="center" p={20}>
            Kosong
          </Text>
        )}
        onEndReachedThreshold={0.2}
        onEndReached={() => {
          if (hasNextPage) fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        renderItem={({ item: reportTarget, index }) => (
          <Div
            alignItems="center"
            mb={10}
            p={10}
            bg="white"
            shadow="sm"
            rounded="sm"
          >
            <ReportInfo
              reportTarget={reportTarget}
              onPress={() => {
                handleTargetDrilldown(
                  navigation,
                  originalFilters,
                  reportTarget,
                  companyId,
                )
              }}
            />
          </Div>
        )}
      />
    </>
  )
}
