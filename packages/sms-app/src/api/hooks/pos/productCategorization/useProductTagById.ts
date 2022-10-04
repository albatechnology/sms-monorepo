import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import {
  ProductTag,
  mapProductTag,
} from "types/POS/ProductCategorization/ProductTag"

import standardErrorHandling, { CustomAxiosErrorType } from "../../../errors"

export default (
  productTagId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<ProductTag, CustomAxiosErrorType>(
    ["productTag", productTagId],
    () => {
      return api
        .productTagShow({ productTag: productTagId.toString() })
        .then((res) => {
          const data: ProductTag = mapProductTag(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
