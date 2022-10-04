import React, { useState } from "react"
import { Div, Text } from "react-native-magnus"

import CompanyDropdownInput from "components/CompanyDropdownInput"
import DatePickerInput from "components/DatePickerInput"
import EnumDropdownInput from "components/EnumDropdownInput"
import ReportableTypeDropdownInput from "components/ReportableTypeDropdownInput"
import ReportableTypeIdDropdownInput from "components/ReportableTypeIdDropdownInput"

import { ActivityFollowUpMethodList, ReportableType } from "api/generated/enums"
import { V1ApiActivityIndexRequest } from "api/openapi"

import { User } from "types/User"
import { filterEnum } from "types/helper"

import FilterBase from "./FilterBase"

export type ActivityFilterType = Writeable<
  Pick<
    V1ApiActivityIndexRequest,
    | "filterCompanyId"
    | "filterChannelId"
    | "filterUserId"
    | "filterFollowUpMethod"
  >
> &
  Writeable<Pick<V1ApiActivityIndexRequest, "filterFollowUpDatetimeAfter">> &
  Writeable<Pick<V1ApiActivityIndexRequest, "filterFollowUpDatetimeBefore">>

type PropTypes = {
  activeFilterValues: ActivityFilterType
  onSetFilter: (newFilter: ActivityFilterType) => void
  userData: User
}

export default function SalesActivityFilter({
  activeFilterValues,
  onSetFilter,
  userData,
}: PropTypes) {
  const [selectedCompanyId, setSelectedCompanyId] = useState(
    activeFilterValues.filterCompanyId || null,
  )
  const [selectedReportableType, setSelectedReportableType] =
    useState<ReportableType>(
      activeFilterValues.filterChannelId || activeFilterValues.filterUserId
        ? activeFilterValues.filterChannelId && activeFilterValues.filterUserId
          ? "CHANNEL"
          : activeFilterValues.filterChannelId
          ? "CHANNEL"
          : "USER"
        : null,
    )
  const [selectedReportableId, setSelectedReportableId] = useState(
    (activeFilterValues.filterChannelId && activeFilterValues.filterUserId) ||
      (activeFilterValues.filterChannelId && !activeFilterValues.filterUserId)
      ? activeFilterValues.filterChannelId.split(",")
      : activeFilterValues.filterUserId?.split(","),
  )
  const [selectedSubReportableId, setSelectedSubReportableId] = useState(
    activeFilterValues.filterChannelId && activeFilterValues.filterUserId
      ? activeFilterValues.filterUserId?.split(",")
      : [],
  )

  return (
    <FilterBase
      onSetFilter={(val) => {
        onSetFilter({
          ...Object.fromEntries(
            Object.entries(val).filter(
              (entry) =>
                ![
                  "filterCompanyId",
                  "filterChannelId",
                  "filterUserId",
                ].includes(entry[0]),
            ),
          ),
          ...(selectedCompanyId ? { filterCompanyId: selectedCompanyId } : {}),
          ...(selectedReportableType === "CHANNEL" &&
          selectedReportableId.length > 0
            ? { filterChannelId: filterEnum(selectedReportableId) }
            : {}),
          ...(selectedReportableType === "USER" &&
          selectedReportableId.length > 0
            ? { filterUserId: filterEnum(selectedReportableId) }
            : {}),
          ...(selectedReportableType === "CHANNEL" &&
          selectedReportableId.length > 0 &&
          selectedSubReportableId.length > 0
            ? { filterUserId: filterEnum(selectedSubReportableId) }
            : {}),
        })
      }}
      onClearFilter={() => {
        setSelectedCompanyId(undefined)
        setSelectedReportableType(null)
        setSelectedReportableId([])
        setSelectedSubReportableId([])
      }}
      activeFilterValues={activeFilterValues}
    >
      {({ setFilter, values }) => (
        <>
          <Text mt={20} mb={10}>
            Company
          </Text>
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
          <Text mt={20} mb={10}>
            Start Date
          </Text>
          <DatePickerInput
            placeholder="Please select date"
            value={
              values.filterFollowUpDatetimeAfter ??
              activeFilterValues?.filterFollowUpDatetimeAfter
            }
            onSelect={(val) =>
              setFilter("filterFollowUpDatetimeAfter")(val.toISOString())
            }
            maximumDate={new Date()}
          />

          <Text mt={20} mb={10}>
            End Date
          </Text>
          <DatePickerInput
            placeholder="Please select date"
            value={
              values.filterFollowUpDatetimeBefore ??
              activeFilterValues?.filterFollowUpDatetimeBefore
            }
            onSelect={(val) =>
              setFilter("filterFollowUpDatetimeBefore")(val.toISOString())
            }
            maximumDate={new Date()}
          />

          <Text mt={20} mb={10}>
            Follow-up Type
          </Text>
          <EnumDropdownInput
            value={
              values.filterFollowUpMethod ??
              activeFilterValues?.filterFollowUpMethod
            }
            onSelect={setFilter("filterFollowUpMethod")}
            title="Follow-up type"
            entityName="Follow-up type"
            enumList={ActivityFollowUpMethodList}
          />
        </>
      )}
    </FilterBase>
  )
}
