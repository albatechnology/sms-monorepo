import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Payment } from "types/Payment/Payment"
import { PaymentType } from "types/Payment/PaymentType"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreatePaymentMutationData = {
  amount: Payment["amount"]
  reference: Payment["reference"]
  paymentTypeId: PaymentType["id"]
  orderId: Payment["orderId"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreatePaymentMutationData>(
    ({
      amount,
      reference,
      paymentTypeId,
      orderId,
    }: CreatePaymentMutationData) => {
      return api.paymentStore({
        data: {
          amount: amount,
          reference: reference,
          payment_type_id: paymentTypeId,
          order_id: orderId,
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("Payment berhasil dibuat")

            queryClient.invalidateQueries("paymentList")
            queryClient.invalidateQueries(["order"])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
