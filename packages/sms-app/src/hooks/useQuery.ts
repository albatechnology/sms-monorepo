import { useIsFocused, useRoute } from "@react-navigation/native"
import {
  useQuery as useReactQuery,
  QueryKey,
  QueryFunction,
  UseQueryOptions,
} from "react-query"

export default function useQuery<
  TQueryFnData = unknown,
  TError = unknown,
  TData = TQueryFnData,
>(
  queryKey: QueryKey,
  queryFn: QueryFunction<TQueryFnData>,
  options?: UseQueryOptions<TQueryFnData, TError, TData>,
) {
  const route = useRoute()
  const focused = useIsFocused()

  // @ts-ignore
  const key = [queryKey, route.name].flat() as TArgs
  return useReactQuery<TQueryFnData, TError, TData>(key, queryFn, {
    enabled: focused,
    ...options,
  })
}
