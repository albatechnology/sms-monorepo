import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Payment } from "types/Payment/Payment"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditPaymentMutationData = {
  id: Payment["id"]
  reference: Payment["reference"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditPaymentMutationData>(
    ({ id, reference }: EditPaymentMutationData) => {
      return api.paymentUpdate({
        payment: id.toString(),
        data: {
          reference,
        },
      })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Payment berhasil dirubah")

            queryClient.invalidateQueries("paymentList")
            queryClient.invalidateQueries(["payment", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
