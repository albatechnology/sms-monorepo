import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Lead } from "types/Lead"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateLeadMutationData = {
  type: Lead["type"]
  label: Lead["label"]
  customerId: Lead["customer"]["id"]
  leadCategoryId: Lead["leadCategory"]["id"]
  isUnhandled: Lead["isUnhandled"]
  interest: Lead["interest"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateLeadMutationData>(
    ({
      type,
      label,
      customerId,
      leadCategoryId,
      isUnhandled,
      interest,
    }: CreateLeadMutationData) => {
      return api.leadStore({
        data: {
          type,
          label,
          customer_id: customerId,
          lead_category_id: leadCategoryId,
          is_unhandled: isUnhandled,
          interest,
        },
      })
    },
    {
      chainSettle: (x, passedVariables: CreateLeadMutationData) =>
        x
          .then((res) => {
            toast("Lead berhasil dibuat")

            queryClient.invalidateQueries("leadListByUser")
            queryClient.invalidateQueries("leadListByUnhandled")
            queryClient.invalidateQueries([
              "leadListByCustomer",
              passedVariables.customerId,
            ])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
