import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type EditCartMutationData = {
  items: { id: number; quantity: number }[]
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, EditCartMutationData>(
    ({ items }: EditCartMutationData) => {
      return api
        .cartSync({
          data: {
            items: items,
          },
        })
        .then((res) => {
          console.log(res)
        })
        .catch((error) => {
          console.log(error.response)
        })
    },
    // {
    //   chainSettle: (x) => x.catch(defaultMutationErrorHandler({})),
    // },
  )

  return mutationData
}
