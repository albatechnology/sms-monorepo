import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Customer } from "types/Customer"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditCustomerMutationData = {
  id: Customer["id"]
  firstName: Customer["firstName"]
  lastName: Customer["lastName"]
  dateOfBirth: Customer["dateOfBirth"]
  email: Customer["email"]
  phone: Customer["phone"]
  description: Customer["description"]
  title: Customer["title"]
  defaultAddressId: Customer["defaultAddressId"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditCustomerMutationData>(
    ({
      id,
      firstName,
      lastName,
      dateOfBirth,
      email,
      phone,
      description,
      title,
      defaultAddressId,
    }: EditCustomerMutationData) => {
      return api.customerUpdate({
        customer: id.toString(),
        data: {
          first_name: firstName,
          last_name: lastName,
          date_of_birth: !!dateOfBirth ? dateOfBirth.toISOString() : null,
          email,
          phone,
          description,
          title,
          default_address_id: defaultAddressId,
        },
      })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Data customer berhasil dirubah")

            queryClient.invalidateQueries("customerList")
            queryClient.invalidateQueries(["customer", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
