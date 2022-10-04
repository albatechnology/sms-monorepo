import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import storage from "../../../storage"
import { HandledAxiosReturn } from "../../errors/index"
import loginErrorHandling from "../../errors/loginError"

type LoginMutationData = {
  email: string
  password: string
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<HandledAxiosReturn, LoginMutationData>(
    ({ email, password }: LoginMutationData) => {
      return storage
        .clearMap()
        .then(() => {
          return api.authToken({ data: { email, password } })
        })
        .then((res) => ({ data: res.data.data.token }))
    },
    {
      chainSettle: (x) => x.catch(loginErrorHandling),
    },
  )

  return mutationData
}
