import { AxiosResponse } from "axios"

type UnwrapPromise<T> = T extends PromiseLike<infer U> ? U : T
type UnwrapAxiosResponse<T> = T extends AxiosResponse<infer U> ? U : T

export type UnwrapOpenAPIResponse<T extends (...args: any) => any> = Partial<
  UnwrapAxiosResponse<UnwrapPromise<ReturnType<T>>>["data"]
>

export function filterEnum<T>(val: T[]): T {
  // @ts-ignore
  return val.join(",")
}
