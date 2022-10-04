import React from "react"
import { Div } from "react-native-magnus"

import { ReportableType } from "api/generated/enums"

import ChannelDropdownInput from "./ChannelDropdownInput"
import UserForReportDropdownInput from "./UserForReportDropdownInput"

type PropTypes = {
  value: any[]
  onSelect: (value) => void
  reportableType: Omit<ReportableType, "COMPANY">
  companyId?: number
  disabled?: boolean
  filterChannelIds?: Array<number>
}

export default ({
  value,
  onSelect,
  reportableType,
  companyId,
  disabled = false,
  filterChannelIds,
}: PropTypes) => {
  if (reportableType === "USER") {
    return (
      <Div row alignItems="center">
        <UserForReportDropdownInput
          value={value}
          onSelect={onSelect}
          disabled={disabled}
          multiple
          extraQueryRequestObject={{ filterCompanyId: companyId }}
          filterChannelIds={filterChannelIds}
        />
      </Div>
    )
  } else if (reportableType === "CHANNEL") {
    return (
      <Div row alignItems="center">
        <ChannelDropdownInput
          value={value}
          onSelect={onSelect}
          disabled={disabled}
          multiple
          extraQueryRequestObject={{ filterCompanyId: companyId }}
          flex={true}
        />
      </Div>
    )
  } else {
    // Shouldn't go here
    return null
  }
}
