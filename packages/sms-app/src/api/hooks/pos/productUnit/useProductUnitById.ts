import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { ProductUnit, mapProductUnit } from "types/POS/ProductUnit/ProductUnit"

import standardErrorHandling, { CustomAxiosErrorType } from "../../../errors"

export default (
  productUnitId: number,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const queryData = useQuery<ProductUnit, CustomAxiosErrorType>(
    ["productUnit", productUnitId],
    () => {
      return api
        .productUnitShow({ productUnit: productUnitId.toString() })
        .then((res) => {
          const data: ProductUnit = mapProductUnit(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
  )

  return queryData
}
