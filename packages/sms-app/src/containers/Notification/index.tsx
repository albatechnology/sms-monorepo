import React from "react"
import { FlatList } from "react-native"

import s from "helper/theme"

import NotificationCard from "./NotificationCard"

const notificationData = [
  {
    id: 1,
    customerName: "Poedi Udi",
    date: "February 26, 2021",
    status: "Follow up again",
  },
  {
    id: 2,
    customerName: "Ibnul Mundzir",
    date: "February 28, 2021",
    status: "Billing",
  },
  {
    id: 3,
    customerName: "Adit",
    date: "February 26, 2021",
    status: "Product sharing",
  },
]

export default () => {
  return (
    <FlatList
      contentContainerStyle={[{ flexGrow: 1 }, s.bgWhite]}
      data={notificationData}
      keyExtractor={({ id }) => `notification_${id}`}
      showsVerticalScrollIndicator={false}
      bounces={false}
      renderItem={({ item: notification, index }) => (
        <NotificationCard
          key={`notification_${index}`}
          notificationData={notification}
        />
      )}
    />
  )
}
