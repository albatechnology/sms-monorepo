import { useMutation } from "react-mutation"
import { MutationParamConfig } from "react-mutation/dist/types"

import defaultMutationErrorHandler from "api/errors/defaultMutationError"

export default function useMutationComponent<TResult, TVariables>(
  fn: (variables: TVariables) => Promise<TResult>,
  overrideConfig?: MutationParamConfig<TVariables>,
) {
  return useMutation<TResult, TVariables>(fn, {
    chainSettle: (chain) => chain.catch(defaultMutationErrorHandler({})),
    ...overrideConfig,
  })
}
