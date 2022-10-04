import { useEffect } from "react"
import { useQuery } from "react-query"

import useApi from "hooks/useApi"

import { Cart, mapCart } from "types/Cart"

import { queryClient } from "../../../query"
import standardErrorHandling from "../../errors"

export default (loggedIn: boolean, extraProps?) => {
  const api = useApi()

  const queryData = useQuery<Cart, string>(
    ["cart", loggedIn],
    () => {
      return api
        .cartIndex()
        .then((res) => {
          const data: Cart = mapCart(res.data.data)
          return data
        })
        .catch(standardErrorHandling)
    },
    { ...extraProps },
  )

  useEffect(() => {
    if (loggedIn) {
      queryClient.invalidateQueries("cart")
    }
  }, [loggedIn])

  return queryData
}
