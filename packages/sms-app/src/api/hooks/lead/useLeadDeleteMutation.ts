import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Lead } from "types/Lead"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type DeleteLeadMutationData = {
  id: Lead["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, DeleteLeadMutationData>(
    ({ id }: DeleteLeadMutationData) => {
      return api.leadDestroy({ lead: id.toString() })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Lead berhasil dihapus")

            queryClient.invalidateQueries("leadListByUser")
            queryClient.invalidateQueries("leadListByCustomer")
            queryClient.invalidateQueries("leadListByUnhandled")
            queryClient.invalidateQueries(["lead", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
