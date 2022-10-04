import { useIsFocused } from "@react-navigation/native"
import { useInfiniteQuery as useReactInfiniteQuery } from "react-query"

import { CustomAxiosErrorType } from "api/errors"

export default function useInfiniteQuery<
  TQueryFnData = unknown,
  TError = CustomAxiosErrorType,
  TData = TQueryFnData,
>(queryKey, queryFunction, config) {
  const focused = useIsFocused()

  return useReactInfiniteQuery<TQueryFnData, TError, TData>(
    queryKey,
    queryFunction,
    {
      enabled: focused,
      ...config,
    },
  )
}
