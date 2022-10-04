import { InfiniteData } from "react-query"

export interface Paginated<T> {
  data: T
  links: {
    first: string
    last: string
    prev: null
    next: null | string
  }
  meta: {
    current_page: number
    from: number
    last_page: number
    path: string
    per_page: number
    to: number
    total: number
  }
}

export function dataFromPaginated<T extends Array<any>>(
  paginationData: InfiniteData<Paginated<T>>,
): T {
  if (!paginationData) return null
  // @ts-ignore
  return paginationData?.pages.reduce(
    (acc, group) => [...acc, ...group.data],
    [],
  )
}

export const handlePaginationFetch = (lastQuery: Paginated<any>) => {
  // Error
  if (lastQuery === undefined) {
    return false
  }

  const currentPage = lastQuery.meta.current_page

  if (!lastQuery.links.next) return false
  return currentPage + 1
}

export const standardExtraQueryParam = {
  getNextPageParam: handlePaginationFetch,
}
