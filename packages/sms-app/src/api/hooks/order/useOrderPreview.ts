import { UseQueryOptions } from "react-query"

import useApi from "hooks/useApi"
import useQuery from "hooks/useQuery"

import { mapOrder, Order } from "types/Order"

import standardErrorHandling, { CustomAxiosErrorType } from "../../errors"

type OrderPreviewData = {
  leadId: number
  items: { id: number; quantity: number }[]
  discountId: Nullable<[]>
  expectedPrice: number
  shippingAddressId: number
  billingAddressId: number
  taxInvoiceId: number
  note: string
  shippingFee: number
  packingFee: number
  additionalDiscount: number
  expectedShippingDateTime: Date
  expectedValidQuotation: Date
  discountType: number
}

export default (
  orderPreviewData: OrderPreviewData,
  extraProps?: UseQueryOptions<any, any, any>,
  customErrorHandler?: (error: CustomAxiosErrorType) => any,
) => {
  const api = useApi()

  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)

  const queryData = useQuery<Order, CustomAxiosErrorType>(
    ["orderPreviewData", orderPreviewData],
    () => {
      const {
        leadId,
        items,
        discountId,
        expectedPrice,
        shippingAddressId,
        billingAddressId,
        taxInvoiceId,
        note,
        shippingFee,
        packingFee,
        additionalDiscount,
        expectedShippingDateTime,
        expectedValidQuotation,
        discountType,
      } = orderPreviewData

      return api
        .orderPreview({
          data: {
            lead_id: leadId,
            items: items,
            discount_ids: discountId,
            expected_price: expectedPrice,
            shipping_address_id: shippingAddressId,
            billing_address_id: billingAddressId,
            tax_invoice_id: taxInvoiceId,
            note: note,
            shipping_fee: shippingFee,
            packing_fee: packingFee,
            additional_discount: additionalDiscount,
            discount_type: discountType,
            quotation_valid_until_datetime: expectedValidQuotation
              ? expectedValidQuotation.toISOString()
              : tomorrow.toISOString(),
            expected_shipping_datetime: expectedShippingDateTime
              ? expectedShippingDateTime.toISOString()
              : tomorrow.toISOString(),
          },
        })
        .then((res) => {
          const data: Order = mapOrder(res.data.data)
          return data
        })
        .catch(customErrorHandler || standardErrorHandling)
    },
    { ...extraProps },
  )

  return queryData
}
