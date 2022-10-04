/* eslint-disable react-hooks/exhaustive-deps */
import { useNavigation } from "@react-navigation/native"
import React, { useEffect, useState } from "react"
import { StyleSheet, View } from "react-native"
import { Button, Div, Text, Select } from "react-native-magnus"

import MonthPickerInput from "components/MonthPickerInput"
import SelectChannel from "components/SelectChannel"
import SelectUserDropdown from "components/SelectUserDropdown"

const FilterScreen = () => {
  const navigation = useNavigation()
  const [filter, setFilter] = useState(undefined)
  const [type, setType] = useState(undefined)
  const [uname, setUname] = useState(undefined)
  const [startDateTime, setStartDateTime] = useState<Date>(null)
  const [endDateTime, setEndDateTime] = useState<Date>(null)
  return (
    <Div bg="white" flex={1}>
      <Div mx={15}>
        <Text my={10} fontWeight="bold">
          Company
        </Text>
        <Div row>
          <Button
            bg="white"
            onPress={() => setFilter("1")}
            borderWidth={1}
            borderColor={filter === "1" ? "#17949D" : "grey"}
            color={filter === "1" ? "#17949D" : "grey"}
            mr={10}
          >
            Melandas
          </Button>
          <Button
            bg="white"
            onPress={() => setFilter("2")}
            borderWidth={1}
            borderColor={filter === "2" ? "#17949D" : "grey"}
            color={filter === "2" ? "#17949D" : "grey"}
          >
            Dio Living
          </Button>
        </Div>
        <Text fontWeight="bold" my={10}>
          Channel Name
        </Text>
        <SelectChannel
          value={type}
          title="Status"
          message="Please select a channel"
          onSelect={(value) => setType(value)}
          id={filter}
        />
        <Text fontWeight="bold" my={10}>
          User Name
        </Text>
        <SelectUserDropdown
          value={uname}
          title="Status"
          message="Please select a user"
          onSelect={(value) => setUname(value)}
          id={type}
          company={filter}
        />
        <Div flexDir="row" mb={10} mt={20}>
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
        <Button
          h={38}
          alignSelf="flex-end"
          bg="#e84118"
          onPress={() => {
            setType(undefined)
            setUname(undefined)
            setStartDateTime(null)
            setEndDateTime(null)
            setFilter("")
          }}
        >
          <Text color="white" fontSize={10}>
            Clear Filter
          </Text>
        </Button>
        <Button
          w={"100%"}
          mt={90}
          mb={10}
          bg="#17949D"
          borderWidth={0}
          borderColor="primary"
          color="white"
          fontSize={14}
          disabled={!!startDateTime || !!endDateTime ? false : true}
          onPress={() =>
            navigation.navigate("Dashboard", {
              filter: filter,
              channel: type,
              sales: uname,
              startDate: startDateTime,
              endDate: endDateTime,
            })
          }
        >
          Apply
        </Button>
      </Div>
    </Div>
  )
}

export default FilterScreen

const styles = StyleSheet.create({})
