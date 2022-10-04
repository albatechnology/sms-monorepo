import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type ApproveOrderDiscountMutationData = {
  orderId: number
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, ApproveOrderDiscountMutationData>(
    ({ orderId }: ApproveOrderDiscountMutationData) => {
      return api.orderApprove({
        order: orderId.toString(),
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("Order berhasil diapprove")

            queryClient.invalidateQueries("orderByWaitingApproval")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
