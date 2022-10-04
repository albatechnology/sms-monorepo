import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Address } from "types/Address"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type DeleteAddressMutationData = {
  id: Address["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, DeleteAddressMutationData>(
    ({ id }: DeleteAddressMutationData) => {
      return api.addressDestroy({ address: id.toString() })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Address berhasil dihapus")

            queryClient.invalidateQueries("addressList")
            queryClient.invalidateQueries(["address", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
