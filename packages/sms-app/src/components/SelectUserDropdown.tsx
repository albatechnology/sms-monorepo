/* eslint-disable react-hooks/exhaustive-deps */
import Case from "case"
import React, { useEffect, useState } from "react"
import { Button, Div, Input, Modal } from "react-native-magnus"
import { useQuery } from "react-query"

import { Select } from "components/Select"
import Text from "components/Text"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

type PropTypes = {
  data: any[]
  title: string
  message: string
  value: string
  onSelect: (value) => void
  disabled?: boolean
  id: string
  company: string
}

export default ({
  data = [],
  title = "",
  message = "",
  value,
  onSelect,
  id = "",
  company = "",
  disabled = false,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [search, setSearch] = useState("")
  const [leadCategory, setLeadCategory] = useState([])
  const FilterUser = useQuery<string, any>(["filterUser", loggedIn], () => {
    return axios
      .get(
        `users?sort=id&filter[company_id]=${company}&filter[type]=SALES&perPage=100&sort=name&filter[channel_id]=${id}&filter[name]=${search}`,
      )
      .then((res) => {
        setLeadCategory(res.data.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })
  useEffect(() => {
    FilterUser.refetch()
  }, [company, id, search])
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
        disabled={disabled}
      >
        {!!value && leadCategory.length >= 1
          ? Case.title(leadCategory.find((x) => x.id === value).name)
          : message}
      </Button>
      <Select
        onSelect={onSelect}
        visible={visible}
        setVisible={setVisible}
        value={value}
        title={title}
        message={
          <>
            <Text mb={5}>Please select sales</Text>
            <Input
              mr={10}
              placeholder="Search by sales name"
              focusBorderColor="primary"
              value={search}
              onChangeText={(val) => {
                setSearch(val)
              }}
            />
          </>
        }
        data={leadCategory}
        renderItem={(item, index) => (
          <Select.Option
            value={item?.id}
            py={20}
            pb={10}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            <Text>{Case.title(item.name)}</Text>
          </Select.Option>
        )}
      />
    </>
  )
}
