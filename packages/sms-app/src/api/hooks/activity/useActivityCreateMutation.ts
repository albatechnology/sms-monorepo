import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Activity } from "types/Activity"
import { Lead } from "types/Lead"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateActivityMutationData = {
  followUpMethod: Activity["followUpMethod"]
  status: Activity["status"]
  brandIds: Activity["brands"][number]["id"][]
  estimatedValue: Activity["estimatedValue"]
  feedback: Activity["feedback"]
  leadId: Lead["id"]
  interiorDesign: any
  reminderDateTime: Activity["reminderDateTime"]
  reminderNote: Activity["reminderNote"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateActivityMutationData>(
    ({
      followUpMethod,
      status,
      brandIds,
      estimatedValue,
      feedback,
      leadId,
      reminderDateTime,
      interiorDesign,
      reminderNote,
    }: CreateActivityMutationData) => {
      return api.activityStore({
        data: {
          follow_up_datetime: new Date().toISOString(),
          follow_up_method: followUpMethod,
          // brand_ids: brandIds,
          // estimated_value: estimatedValue,
          estimations: estimatedValue,
          interior_design_id: interiorDesign,
          status,
          feedback,
          lead_id: leadId,
          reminder_datetime: !!reminderDateTime
            ? reminderDateTime.toISOString()
            : null,
          reminder_note: !!reminderDateTime ? reminderNote : "",
        },
      })
    },
    {
      chainSettle: (x, passedVariables: CreateActivityMutationData) =>
        x
          .then((res) => {
            queryClient.invalidateQueries("activityList")
            // DEBT: Invalidate only specific customer
            queryClient.invalidateQueries(["activityListByCustomer"])

            // This changes the status/priority of lead, so we need to invalidate that too
            queryClient.invalidateQueries("lead")
            queryClient.invalidateQueries("leadListByCustomer")
            queryClient.invalidateQueries("leadListByUnhandled")
            queryClient.invalidateQueries("leadListByUser")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
