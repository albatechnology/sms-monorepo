import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Lead } from "types/Lead"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditLeadMutationData = {
  id: Lead["id"]
  type: Lead["type"]
  label: Lead["label"]
  customerId: Lead["customer"]["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditLeadMutationData>(
    ({ id, type, label, customerId }: EditLeadMutationData) => {
      return api.leadUpdate({
        lead: id.toString(),
        data: {
          type,
          label,
          customer_id: customerId,
        },
      })
    },
    {
      chainSettle: (x, passedVariables: EditLeadMutationData) =>
        x
          .then((res) => {
            toast("Data lead berhasil dirubah")

            queryClient.invalidateQueries("leadListByUser")
            queryClient.invalidateQueries([
              "leadListByCustomer",
              passedVariables.customerId,
            ])
            queryClient.invalidateQueries("leadListByUnhandled")
            queryClient.invalidateQueries(["lead", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
