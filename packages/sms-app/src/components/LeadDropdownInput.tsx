import Case from "case"
import React, { useMemo, useState } from "react"
import { Button, Div, Input } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useDebounce from "hooks/useDebounce"
import useMultipleQueries from "hooks/useMultipleQueries"

import useLeadsListByUser from "api/hooks/lead/useLeadsListByUser"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

import { getFullName } from "types/Customer"
import { Lead, leadStatusConfig } from "types/Lead"
import { filterEnum } from "types/helper"

type PropTypes = {
  value: string
  onSelect: (value) => void
  disabled?: boolean
}

export default ({ value, onSelect, disabled }: PropTypes) => {
  const [visible, setVisible] = useState(false)

  const [filter, setFilter] = useState(null)

  const debouncedVal = useDebounce(filter, 500)

  const {
    queries: [{ data: leadPaginatedData }],
    meta,
  } = useMultipleQueries(
    [
      useLeadsListByUser({
        filterCustomerSearch: debouncedVal || "",
        filterType: filterEnum(["LEADS", "PROSPECT"]),
        sort: "-id",
      }),
    ] as const,
    {
      useStandardIsLoadingBehaviour: true,
    },
  )

  const { isError, isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } =
    meta

  const data: Lead[] = dataFromPaginated(leadPaginatedData)

  const activeLead = useMemo(
    () => (!!data ? data.find((x) => x.id === parseInt(value, 10)) : null),
    [data, value],
  )

  return (
    <>
      <Button
        block
        borderWidth={1}
        bg="white"
        color={value ? "primary" : COLOR_PLACEHOLDER}
        fontSize={11}
        py={13}
        borderColor="grey"
        justifyContent="flex-start"
        onPress={() => setVisible(!visible)}
        disabled={disabled || isLoading}
      >
        {!!activeLead ? (
          <Text>{activeLead.label}</Text>
        ) : (
          "Click to select a lead / prospect"
        )}
      </Button>
      <Select
        isLoading={isLoading}
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title="Lead/Prospect List"
        message={
          <>
            <Text mb={5}>Search Lead:</Text>
            <Input
              mr={10}
              placeholder="Search by name/phone/email"
              focusBorderColor="primary"
              value={filter}
              onChangeText={(val) => {
                setFilter(val)
              }}
            />
          </>
        }
        data={data || []}
        onEndReached={() => {
          hasNextPage && fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `lead_${item.id}`}
        renderItem={(item, index) => (
          <Select.Option
            value={item.id.toString()}
            py={20}
            pb={10}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            {renderLead(item)}
          </Select.Option>
        )}
      />
    </>
  )
}

const renderLead = (lead: Lead) => {
  return (
    <>
      <Text fontWeight="bold" mb={5}>
        {Case.title(lead?.type)}: {lead?.label}
      </Text>
      <Text mb={5}>{getFullName(lead.customer)}</Text>
      <Text mb={5}>{lead.customer.email}</Text>
      <Text mb={5}>{lead.customer.phone}</Text>
      <Div py={5} px={10} w={"35%"} bg={leadStatusConfig[lead.status].bg}>
        <Text
          textAlign="center"
          color={leadStatusConfig[lead.status].textColor}
        >
          {leadStatusConfig[lead.status].displayText}
        </Text>
      </Div>
    </>
  )
}
