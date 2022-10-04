/* eslint-disable react-hooks/exhaustive-deps */
import Case from "case"
import React, { useEffect, useState } from "react"
import { Button, Div, Input, Modal } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import { Select } from "components/Select"
import Text from "components/Text"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import useLocationStore from "api/hooks/order/useLocationStore"

import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

import Loading from "./Loading"

type PropTypes = {
  data?: any[]
  title: string
  sku: string
  companyId?: number
  message: string
  value: string
  onSelect: (value) => void
  disabled?: boolean
}

export default ({
  data = [],
  title = "",
  sku = "",
  companyId,
  message = "",
  value,
  onSelect,
  disabled = false,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [search, setSearch] = useState("")
  const [leadCategory, setLeadCategory] = useState([])
  const {
    queries: [{ data: locationData }],
    meta: { isLoading, isError, refetch },
  } = useMultipleQueries([useLocationStore(sku, companyId)] as const)
  if (isLoading) {
    return <Loading />
  }
  const warehouse = locationData?.data?.stocks
  return (
    <>
      <Button
        block
        borderWidth={1}
        bg="white"
        color={!value ? "primary" : COLOR_PLACEHOLDER}
        fontSize={11}
        py={13}
        borderColor="grey"
        justifyContent="flex-start"
        onPress={() => setVisible(!visible)}
        disabled={disabled}
      >
        <Text>
          {!!value && warehouse.length >= 1
            ? Case.title(
                warehouse.find((x) => x?.orlan_id === value?.orlan_id).name,
              )
            : message}
        </Text>
      </Button>
      <Select
        onSelect={onSelect}
        visible={visible}
        setVisible={setVisible}
        value={value}
        title={title}
        message={
          <>
            <Text mb={5}>Please select Warehouse location</Text>
          </>
        }
        data={warehouse}
        renderItem={(item, index) => (
          <Select.Option
            value={item}
            py={20}
            pb={10}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            <Text>
              {Case.title(item.name)}{" "}
              <Text color="#17949D">{`(${item.stock || "0"})`}</Text>
            </Text>
          </Select.Option>
        )}
      />
    </>
  )
}
