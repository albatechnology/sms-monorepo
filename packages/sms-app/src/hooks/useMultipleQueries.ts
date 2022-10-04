import { useCallback, useEffect, useMemo, useState } from "react"
import {
  InfiniteQueryObserverBaseResult,
  QueryObserverResult,
} from "react-query"

export type MultipleQueriesConfig = {
  /** Determains whether we check for the data */
  useStandardIsLoadingBehaviour?: boolean
}

export type MultipleQueriesReturnType<TQueries> = {
  queries: TQueries
  meta: MultipleQueriesMetaReturnType
}

export type MultipleQueriesMetaReturnType = {
  errors: any
  isError: boolean
  isFetchedAfterMount: boolean
  isManualRefetching: boolean
  isFetching: boolean
  isFetchingNextPage: boolean
  isLoading: boolean
  hasNextPage: boolean
  fetchNextPage: () => any
  refetch: () => Promise<any | undefined>[]
  manualRefetch: () => void
}

const allTrueReduce = [(acc, val) => acc && val, true] as const
const anyTrueReduce = [(acc, val) => acc || val, false] as const

export default function useMultipleQueries<
  TQueries extends readonly (
    | InfiniteQueryObserverBaseResult<any, any>
    | QueryObserverResult<any, any>
  )[],
>(
  queries: TQueries,
  config?: MultipleQueriesConfig,
): MultipleQueriesReturnType<TQueries> {
  const [isManualRefetch, setIsManualRefetch] = useState(false)

  const isError = useMemo(
    () => queries.map((x) => x.isError).reduce(...anyTrueReduce),
    [queries],
  )
  const isFetchedAfterMount = useMemo(
    () => queries.map((x) => x.isFetchedAfterMount).reduce(...allTrueReduce),
    [queries],
  )
  const isFetching = useMemo(
    () => queries.map((x) => x.isFetching).reduce(...anyTrueReduce),
    [queries],
  )
  const isFetchingNextPage = useMemo(
    () =>
      queries
        .map((x) =>
          !!x["isFetchingNextPage"] ? x["isFetchingNextPage"] : false,
        )
        .reduce(...anyTrueReduce),
    [queries],
  )
  const isLoading = useMemo(
    () =>
      config?.useStandardIsLoadingBehaviour
        ? queries.map((x) => x.isLoading).reduce(...anyTrueReduce)
        : queries
            .map((x) => x.isLoading || x.data === undefined)
            .reduce(...anyTrueReduce),
    [queries, config],
  )
  const hasNextPage = useMemo(
    () =>
      queries
        .map((x) => (!!x["hasNextPage"] ? x["hasNextPage"] : false))
        .reduce(...anyTrueReduce),
    [queries],
  )

  const errors = useMemo(() => queries.map((x) => x.error), [queries])

  const fetchNextPage = useCallback(
    () =>
      queries.map((x) => (!!x["fetchNextPage"] ? x["fetchNextPage"]() : false)),
    [queries],
  )
  const refetch = useCallback(() => queries.map((x) => x.refetch()), [queries])

  useEffect(() => {
    if (!isFetching) {
      setIsManualRefetch(false)
    }
  }, [isFetching])

  const manualRefetch = useCallback(() => {
    setIsManualRefetch(true)
    refetch()
  }, [refetch])

  return {
    queries,
    meta: {
      errors,
      isError,
      isFetching,
      isFetchingNextPage,
      isFetchedAfterMount,
      isManualRefetching: isFetching && isManualRefetch,
      isLoading,
      hasNextPage,
      fetchNextPage,
      refetch,
      manualRefetch,
    },
  }
}
