import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Address } from "types/Address"
import { Customer } from "types/Customer"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateCustomerWithAddressMutationData = {
  firstName: Customer["firstName"]
  lastName: Customer["lastName"]
  dateOfBirth: Customer["dateOfBirth"]
  email: Customer["email"]
  phone: Customer["phone"]
  description: Customer["description"]
  title: Customer["title"]
  addressLine1: Address["addressLine1"]
  addressLine2: Address["addressLine2"]
  addressLine3: Address["addressLine3"]
  postcode: Address["postcode"]
  city: Address["city"]
  country: Address["country"]
  province: Address["province"]
  type: Address["type"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateCustomerWithAddressMutationData>(
    ({
      firstName,
      lastName,
      dateOfBirth,
      email,
      phone,
      description,
      title,
      addressLine1,
      addressLine2,
      addressLine3,
      postcode,
      city,
      country,
      province,
      type,
    }: CreateCustomerWithAddressMutationData) => {
      return api.customerStoreWithAddress({
        data: {
          first_name: firstName,
          last_name: lastName,
          date_of_birth: !!dateOfBirth ? dateOfBirth.toISOString() : null,
          email: email,
          phone: phone,
          description: description,
          title: title,
          address_line_1: addressLine1,
          address_line_2: addressLine2,
          address_line_3: addressLine3,
          postcode: postcode,
          city: city,
          country: country,
          province: province,
          type: type,
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("Customer berhasil dibuat")

            queryClient.invalidateQueries("customerList")
            queryClient.invalidateQueries("addressList")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
