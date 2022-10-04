import React from "react"
import { Avatar, Div } from "react-native-magnus"

import Text from "components/Text"

import { formatDate, responsive } from "helper"
import { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

import { ActivityComment } from "types/ActivityComment"

type PropTypes = {
  comment: ActivityComment
}

export default ({ comment }: PropTypes) => {
  return (
    <Div px={20} py={10} bg="white" row>
      <Avatar
        bg={COLOR_DISABLED}
        color={COLOR_PRIMARY}
        size={responsive(30)}
        mr={10}
      >
        {`${comment?.user.name[0]}`}
      </Avatar>
      <Div flex={1}>
        <Div row justifyContent="space-between" mb={5}>
          <Text fontWeight="bold">{comment?.user?.name}</Text>
          <Text fontSize={10}>{formatDate(comment?.createdAt)}</Text>
        </Div>

        <Div p={10} bg={COLOR_DISABLED} rounded={4}>
          <Text>{comment?.content}</Text>
        </Div>
      </Div>
    </Div>
  )
}
