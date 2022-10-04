import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Activity } from "types/Activity"
import { Lead } from "types/Lead"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditActivityMutationData = {
  id: Activity["id"]
  followUpDatetime: Activity["followUpDatetime"]
  followUpMethod: Activity["followUpMethod"]
  status: Activity["status"]
  brandIds: Activity["brands"][number]["id"][]
  estimatedValue: Activity["estimatedValue"]
  feedback: Activity["feedback"]
  leadId: Lead["id"]
  reminderDateTime: Activity["reminderDateTime"]
  reminderNote: Activity["reminderNote"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditActivityMutationData>(
    ({
      id,
      followUpDatetime,
      followUpMethod,
      status,
      brandIds,
      estimatedValue,
      feedback,
      leadId,
      reminderDateTime,
      reminderNote,
    }: EditActivityMutationData) => {
      return api.activityUpdate({
        activity: id.toString(),
        data: {
          follow_up_datetime: followUpDatetime.toISOString(),
          follow_up_method: followUpMethod,
          brand_ids: brandIds,
          estimated_value: estimatedValue,
          status,
          feedback,
          lead_id: leadId,
          reminder_datetime: !!reminderDateTime
            ? reminderDateTime.toISOString()
            : null,
          reminder_note: reminderNote,
        },
      })
    },
    {
      chainSettle: (x, passedVariables: EditActivityMutationData) =>
        x
          .then((res) => {
            toast("Data activity berhasil dirubah")

            queryClient.invalidateQueries("activityList")
            queryClient.invalidateQueries(["activityListByCustomer"])
            queryClient.invalidateQueries(["activity", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
