import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  useNavigation,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { MutableRefObject, useEffect, useMemo, useState } from "react"
import { ActivityIndicator, Pressable } from "react-native"
import { Button, Div, Icon, Skeleton } from "react-native-magnus"

import CompanyDropdownInput from "components/CompanyDropdownInput"
import ErrorComponent from "components/Error"
import MonthPickerInput from "components/MonthPickerInput"
import ReportableTypeDropdownInput from "components/ReportableTypeDropdownInput"
import ReportableTypeIdDropdownInput from "components/ReportableTypeIdDropdownInput"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { ReportableType, TargetType } from "api/generated/enums"
import useReportTargetList from "api/hooks/reportTarget/useReportTargetList"
import { V1ApiTargetIndexRequest } from "api/openapi"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  DashboardStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import { responsive } from "helper"
import {
  getEndOfMonthUTCFormatted,
  getStartOfMonthUTCFormatted,
} from "helper/datetime"

import {
  handleTargetDrilldown,
  processReportTarget,
  ReportTarget,
} from "types/ReportTarget"
import { User } from "types/User"
import { filterEnum } from "types/helper"

import { ReportInfo } from "./ReportInfo"

type CurrentScreenNavigationProp = CompositeNavigationProp<
  StackNavigationProp<DashboardStackParamList, "DashBoard">,
  CompositeNavigationProp<
    StackNavigationProp<EntryStackParamList>,
    BottomTabNavigationProp<MainTabParamList>
  >
>

type PropTypes = {
  userData: User
}

export default React.forwardRef<any, PropTypes>(({ userData }, ref) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()
  const [startDateTime, setStartDateTime] = useState<Date>(null)
  const [endDateTime, setEndDateTime] = useState<Date>(null)
  const [selectedCompanyId, setSelectedCompanyId] = useState(
    userData?.company?.id,
  )
  const [selectedReportableType, setSelectedReportableType] =
    useState<ReportableType>(null)
  const [selectedReportableId, setSelectedReportableId] = useState([])
  const [selectedSubReportableId, setSelectedSubReportableId] = useState([])

  // Set default filter options for SALES
  useEffect(() => {
    if (userData.type === "SALES" || userData.type === "SUPERVISOR") {
      setSelectedReportableType("USER")
      setSelectedReportableId([userData.id])
      setSelectedSubReportableId([])
    }
  }, [userData])

  const shouldShowOtherReportable =
    selectedReportableType && selectedReportableId.length > 0

  const computedFilters: V1ApiTargetIndexRequest = {
    filterReportableType: shouldShowOtherReportable
      ? selectedSubReportableId.length > 0
        ? "USER"
        : selectedReportableType
      : "COMPANY",
    ...(shouldShowOtherReportable
      ? {
          filterReportableIds:
            selectedSubReportableId.length > 0
              ? filterEnum(selectedSubReportableId)
              : filterEnum(selectedReportableId),
        }
      : { filterReportableIds: selectedCompanyId ?? undefined }),
    ...(startDateTime && endDateTime
      ? {
          filterStartAfter: getStartOfMonthUTCFormatted(startDateTime),
          filterEndBefore: getEndOfMonthUTCFormatted(endDateTime),
        }
      : {}),
  }

  const {
    queries: [reportTargetsQuery],
    meta: { isError, isLoading: reportQueriesIsLoading, isFetching, refetch },
  } = useMultipleQueries([useReportTargetList(computedFilters)] as const, {
    useStandardIsLoadingBehaviour: true,
  })

  useEffect(() => {
    if (ref) {
      ;(ref as MutableRefObject<any>).current = {}
      ;(ref as MutableRefObject<any>).current.refetch = refetch
    }
  }, [ref, refetch])

  const rawReportTargets = useMemo(
    () => reportTargetsQuery.data ?? [],
    [reportTargetsQuery],
  )

  const reportTargets = useMemo(() => {
    return processReportTarget(rawReportTargets)
  }, [rawReportTargets])

  const targetTypeList = reportTargets.reduce((acc, reportTarget) => {
    if (!!acc.find((x) => x === reportTarget.type)) {
      return acc
    }
    return [...acc, reportTarget.type]
  }, [] as TargetType[])

  const isNotSales = userData.type !== "SALES"

  if (isError) {
    return <ErrorComponent refreshing={isFetching} onRefresh={refetch} />
  }
  return (
    <Div p={20} mb={5} bg="white">
      {isNotSales && (
        <>
          <Button
            block
            bg="white"
            borderColor="grey"
            borderWidth={1}
            mb={20}
            justifyContent="space-between"
            prefix={
              <Icon
                fontSize={responsive(14)}
                name="activity"
                color="primary"
                fontFamily="Feather"
              />
            }
            suffix={
              <Icon
                name="chevron-forward"
                color="primary"
                fontSize={responsive(14)}
                fontFamily="Ionicons"
              />
            }
            color="primary"
            fontSize={14}
            onPress={() => navigation.navigate("SalesActivity")}
          >
            Sales Activity
          </Button>
          <Div row alignItems="center" mb={10}>
            <Icon
              pr={5}
              fontSize={responsive(16)}
              name="filter"
              color="primary"
              fontFamily="Feather"
            />
            <Text fontSize={14} fontWeight="bold">
              Filter
            </Text>
          </Div>

          <Div flexDir="row" mb={10}>
            <Div flex={1} mr={10}>
              <MonthPickerInput
                placeholder="Start Month"
                onSelect={setStartDateTime}
                value={startDateTime}
              />
            </Div>
            <Div flex={1}>
              <MonthPickerInput
                placeholder="End Month"
                onSelect={setEndDateTime}
                value={endDateTime}
              />
            </Div>
          </Div>
          <Div mb={10}>
            <CompanyDropdownInput
              value={selectedCompanyId}
              onSelect={(val) => {
                setSelectedCompanyId(val)
                setSelectedReportableId([])
                setSelectedSubReportableId([])
              }}
            />
          </Div>
          <Div flexDir="row" mb={10}>
            {!!selectedCompanyId && (
              <Div flex={1}>
                <ReportableTypeDropdownInput
                  value={selectedReportableType}
                  onSelect={(val) => {
                    setSelectedReportableType(val)
                    setSelectedReportableId([])
                    setSelectedSubReportableId([])
                  }}
                />
              </Div>
            )}
            {!!selectedCompanyId && selectedReportableType && (
              <Div flex={1} ml={10}>
                <ReportableTypeIdDropdownInput
                  companyId={selectedCompanyId}
                  reportableType={selectedReportableType}
                  value={selectedReportableId}
                  onSelect={(val) => {
                    setSelectedReportableId(val)
                    setSelectedSubReportableId([])
                  }}
                />
              </Div>
            )}
          </Div>
          {/* If we select a channel reportable, then we show the user list from that user */}
          {selectedReportableType === "CHANNEL" &&
            !!selectedReportableId &&
            selectedReportableId.length > 0 && (
              <ReportableTypeIdDropdownInput
                companyId={selectedCompanyId}
                reportableType="USER"
                value={selectedSubReportableId}
                onSelect={setSelectedSubReportableId}
                filterChannelIds={selectedReportableId.map((x) =>
                  parseInt(x, 10),
                )}
              />
            )}
        </>
      )}

      <Div mt={20}>
        <Div
          flexDir="row"
          alignItems="center"
          justifyContent="space-between"
          mb={10}
        >
          <Div row alignItems="center">
            <Icon
              pr={5}
              fontSize={responsive(16)}
              name="book"
              color="primary"
              fontFamily="AntDesign"
            />
            <Text fontSize={14} fontWeight="bold">
              Report
            </Text>
          </Div>
          {isFetching ? (
            <Button bg="white" disabled>
              <ActivityIndicator color="black" />
            </Button>
          ) : (
            <Button bg="white" onPress={refetch}>
              <Icon
                pl={5}
                fontSize={responsive(14)}
                name="reload"
                color="primary"
                fontFamily="Ionicons"
              />
            </Button>
          )}
        </Div>
        {!!reportQueriesIsLoading ? (
          <Skeleton.Box w="100%" h={70} mb={5} />
        ) : (
          targetTypeList &&
          targetTypeList.map((targetType, i) => {
            return (
              <ReportTargetCard
                key={`${targetType}_${i}`}
                targetType={targetType}
                reportTargets={reportTargets}
                computedFilters={computedFilters}
              />
            )
          })
        )}
      </Div>
    </Div>
  )
})

type ReportTargetCardPropTypes = {
  targetType: TargetType
  reportTargets: ReportTarget[]
  computedFilters: V1ApiTargetIndexRequest
}
const ReportTargetCard = ({
  targetType,
  reportTargets,
  computedFilters,
}: ReportTargetCardPropTypes) => {
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [opened, setOpened] = useState(false)

  const thisReportTargetList = reportTargets.filter(
    (reportTarget) => reportTarget.type === targetType,
  )

  const showChevron = thisReportTargetList.length > 1

  return (
    <>
      <Div
        alignItems="center"
        mb={10}
        p={10}
        bg="white"
        shadow="sm"
        rounded="sm"
      >
        {(opened ? thisReportTargetList : [thisReportTargetList[0]])
          .map((reportTarget, j, arr) => (
            <>
              <ReportInfo
                key={j}
                reportTarget={reportTarget}
                onPress={() => {
                  handleTargetDrilldown(
                    navigation,
                    computedFilters,
                    reportTarget,
                  )
                }}
              />
              {arr.length - 1 !== j ? (
                <Div
                  key={j}
                  w="100%"
                  my={5}
                  borderBottomWidth={1}
                  borderColor="grey"
                  borderStyle="solid"
                />
              ) : null}
            </>
          ))
          .flat()}

        {showChevron && (
          <Pressable
            style={{ width: "100%", paddingTop: 8 }}
            onPress={() => setOpened((x) => !x)}
          >
            <Icon
              name={!opened ? "chevron-down" : "chevron-up"}
              fontSize={14}
              fontFamily="FontAwesome5"
              color="black"
            />
          </Pressable>
        )}
      </Div>
    </>
  )
}
