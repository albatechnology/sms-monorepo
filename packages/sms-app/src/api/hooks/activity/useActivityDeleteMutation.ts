import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { Activity } from "types/Activity"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type DeleteActivityMutationData = {
  id: Activity["id"]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, DeleteActivityMutationData>(
    ({ id }: DeleteActivityMutationData) => {
      return api.activityDestroy({ activity: id.toString() })
    },
    {
      chainSettle: (x, passedVariables) =>
        x
          .then((res) => {
            toast("Activity berhasil dihapus")

            queryClient.invalidateQueries("activityList")
            queryClient.invalidateQueries("activityListByCustomer")
            queryClient.invalidateQueries(["activity", passedVariables.id])
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
