import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { ProductModel, mapProductModel } from "types/POS/Product/ProductModel"

import standardErrorHandling, { CustomAxiosErrorType } from "../../../errors"

export default (
  productModelId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<ProductModel, CustomAxiosErrorType>(
    ["productModel", productModelId],
    () => {
      return api
        .productModelById({ model: productModelId.toString() })
        .then((res) => {
          const data: ProductModel = mapProductModel(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
