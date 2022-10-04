import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Customer } from "types/Customer"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateCustomerMutationData = {
  firstName: Customer["firstName"]
  lastName: Customer["lastName"]
  dateOfBirth: Customer["dateOfBirth"]
  email: Customer["email"]
  phone: Customer["phone"]
  description: Customer["description"]
  title: Customer["title"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateCustomerMutationData>(
    ({
      firstName,
      lastName,
      dateOfBirth,
      email,
      phone,
      description,
      title,
    }: CreateCustomerMutationData) => {
      return api.customerStore({
        data: {
          first_name: firstName,
          last_name: lastName,
          date_of_birth: dateOfBirth.toISOString(),
          email,
          phone,
          description,
          title,
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("Customer berhasil dibuat")

            queryClient.invalidateQueries("customerList")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
