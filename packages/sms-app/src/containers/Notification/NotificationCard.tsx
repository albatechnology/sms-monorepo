import React from "react"
import { Div, Icon } from "react-native-magnus"

import Text from "components/Text"

export default ({ notificationData: { date, customerName, status } }) => {
  return (
    <Div
      row
      bg="white"
      p={20}
      borderBottomWidth={0.8}
      borderBottomColor="grey"
      alignItems="flex-start"
    >
      <Icon
        name="notifications-circle"
        fontSize={40}
        mr={10}
        color="primary"
        fontFamily="Ionicons"
      />
      <Div>
        <Text color="grey" mb={5}>
          {date}
        </Text>
        <Text fontSize={14} fontWeight="bold" mb={5}>
          {customerName}
        </Text>
        <Text color="grey">{status}</Text>
      </Div>
    </Div>
  )
}
