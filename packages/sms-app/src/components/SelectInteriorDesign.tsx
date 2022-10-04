/* eslint-disable react-hooks/exhaustive-deps */
import Case from "case"
import React, { useEffect, useState } from "react"
import { Button, Div, Input, Modal } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"
import { useQuery } from "react-query"

import { Select } from "components/Select"
import Text from "components/Text"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { COLOR_DISABLED, COLOR_PLACEHOLDER } from "helper/theme"

type PropTypes = {
  data?: any[]
  title: string
  message: string
  value: string
  onSelect: (value) => void
  disabled?: boolean
  id: number
}

export default ({
  data = [],
  title = "",
  message = "",
  value,
  onSelect,
  id = null,
  disabled = false,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [search, setSearch] = useState("")
  const [leadCategory, setLeadCategory] = useState([])
  const interiorDesign = useQuery<string, any>(["interior", loggedIn], () => {
    return axios
      .get(`interior-designs?page=1&perPage=30&sort=-id`)
      .then((res) => {
        setLeadCategory(res?.data?.data)
      })
      .catch((error) => {
        if (error.response) {
          console.log(error.response)
        }
      })
  })
  useEffect(() => {
    interiorDesign.refetch()
  }, [id, search])
  return (
    <>
      <Div bg="white" py={15}>
        <Text
          fontSize={14}
          style={{ marginLeft: widthPercentageToDP(5) }}
          fontWeight="bold"
          mb={10}
        >
          Interior Design
        </Text>
        <Button
          block
          borderTopWidth={1}
          rounded={0}
          bg="white"
          color={value ? "primary" : COLOR_PLACEHOLDER}
          fontSize={11}
          py={13}
          borderColor="grey"
          justifyContent="flex-start"
          onPress={() => setVisible(!visible)}
          disabled={disabled}
        >
          <Text ml={10}>
            {!!value && leadCategory.length >= 1
              ? Case.title(leadCategory.find((x) => x.id === value).name)
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
              <Text mb={5}>Please select Interior Design</Text>
              {/* <Input
              mr={10}
              placeholder="Search by channel name"
              focusBorderColor="primary"
              value={search}
              onChangeText={(val) => {
                setSearch(val)
              }}
            /> */}
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
      </Div>
    </>
  )
}
