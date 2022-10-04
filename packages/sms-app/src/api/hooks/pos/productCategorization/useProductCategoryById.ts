import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import {
  ProductCategory,
  mapProductCategory,
} from "types/POS/ProductCategorization/ProductCategory"

import standardErrorHandling, { CustomAxiosErrorType } from "../../../errors"

export default (
  productCategoryId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<ProductCategory, CustomAxiosErrorType>(
    ["productCategory", productCategoryId],
    () => {
      return api
        .productCategoryShow({ productCategory: productCategoryId.toString() })
        .then((res) => {
          const data: ProductCategory = mapProductCategory(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
