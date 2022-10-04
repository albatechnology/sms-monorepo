import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import defaultMutationErrorHandler from "api/errors/defaultMutationError"

import { Order } from "types/Order"

import { queryClient } from "../../../query"

type CloneOrderDiscountMutationData = {
  orderId: Order["id"]
  note?: Order["note"]
  additionalDiscount?: Order["additionalDiscount"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CloneOrderDiscountMutationData>(
    ({ orderId, note, additionalDiscount }: CloneOrderDiscountMutationData) => {
      return api.orderClone({
        order: orderId.toString(),
        data: {
          ...(note ? { note } : {}),
          ...(additionalDiscount
            ? { additional_discount: additionalDiscount }
            : {}),
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("Order berhasil ter-clone")

            queryClient.invalidateQueries("order")
            queryClient.invalidateQueries("orderByWaitingApproval")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
