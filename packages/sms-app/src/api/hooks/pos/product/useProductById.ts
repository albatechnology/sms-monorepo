import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { Product, mapProduct } from "types/POS/Product/Product"

import standardErrorHandling, { CustomAxiosErrorType } from "../../../errors"

export default (
  productId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<Product, CustomAxiosErrorType>(
    ["product", productId],
    () => {
      return api
        .productShow({ product: productId.toString() })
        .then((res) => {
          const data: Product = mapProduct(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
