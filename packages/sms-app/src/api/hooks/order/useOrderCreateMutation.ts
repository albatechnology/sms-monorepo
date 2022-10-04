import useApi from "hooks/useApi"
import useMutation from "hooks/useMutation"

import { queryClient } from "../../../query"
import defaultMutationErrorHandler from "../../errors/defaultMutationError"

type CreateOrderMutationData = {
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
  expectedShippingDatetime: Date
  interiorDesignId: number
  expectedValidQuotation: Date
  discountType: number
  isDirectorPurchase?: boolean
}

export default () => {
  const api = useApi()

  const mutationData = useMutation<any, CreateOrderMutationData>(
    ({
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
      interiorDesignId,
      additionalDiscount,
      expectedShippingDatetime,
      expectedValidQuotation,
      discountType,
      isDirectorPurchase,
    }: CreateOrderMutationData) => {
      return api.orderStore({
        data: {
          lead_id: leadId,
          items: items,
          interior_design_id: interiorDesignId,
          discount_ids: discountId,
          expected_price: expectedPrice,
          shipping_address_id: shippingAddressId,
          billing_address_id: billingAddressId,
          tax_invoice_id: taxInvoiceId,
          note: note,
          shipping_fee: shippingFee,
          discount_type: discountType,
          packing_fee: packingFee,
          additional_discount: additionalDiscount,
          is_direct_purchase: isDirectorPurchase,
          quotation_valid_until_datetime: expectedValidQuotation.toISOString(),
          expected_shipping_datetime: !!expectedShippingDatetime
            ? expectedShippingDatetime.toISOString()
            : null,
        },
      })
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("Order berhasil dibuat")
            queryClient.invalidateQueries("order")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )

  return mutationData
}
