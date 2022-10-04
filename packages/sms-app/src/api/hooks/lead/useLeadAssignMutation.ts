import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Lead } from "types/Lead"
import { User } from "types/User"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type AssignLeadMutationData = {
  id: Lead["id"]
  userId: User["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, AssignLeadMutationData>(
    ({ id, userId }: AssignLeadMutationData) => {
      return api.leadAssign({ lead: id.toString(), data: { user_id: userId } })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Lead berhasil diassign")

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
