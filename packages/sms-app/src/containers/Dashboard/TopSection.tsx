import React from "react"
import { Div, Skeleton } from "react-native-magnus"

import CartItem from "containers/Cart/CartItem"

import Text from "components/Text"

export default ({ userData, channelData }) => {
  return (
    <Div row flex={1} justifyContent="center" p={20} mb={5} bg="#313132">
      <Div alignItems="center">
        {userData?.name ? (
          <Text fontSize={14} color="#17949D" fontWeight="bold" mb={5}>
            {userData?.name}
          </Text>
        ) : (
          <Skeleton.Box w={100} h={20} mb={5} m={0} />
        )}
        {userData?.email ? (
          <Text color="white">{userData?.email}</Text>
        ) : (
          <Skeleton.Box w={140} mb={5} />
        )}
      </Div>
    </Div>
  )
}
