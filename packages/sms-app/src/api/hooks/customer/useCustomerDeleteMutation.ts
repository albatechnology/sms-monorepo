import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Customer } from "types/Customer"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type DeleteCustomerMutationData = {
  id: Customer["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, DeleteCustomerMutationData>(
    ({ id }: DeleteCustomerMutationData) => {
      return api.customerDelete({ customer: id.toString() })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Customer berhasil dihapus")

            queryClient.invalidateQueries("customerList")
            queryClient.invalidateQueries(["customer", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
