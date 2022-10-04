import React from "react"
import { Button } from "react-native-magnus"

import { COLOR_PRIMARY } from "helper/theme"

type PropTypes = {
  fetchNextPage: () => void
  isFetchingNextPage: boolean
}

export default ({ fetchNextPage, isFetchingNextPage = false }: PropTypes) => (
  <Button
    mt={10}
    w={"80%"}
    color="white"
    bg={COLOR_PRIMARY}
    alignSelf="center"
    onPress={() => fetchNextPage()}
    loading={isFetchingNextPage}
  >
    Load more
  </Button>
)
