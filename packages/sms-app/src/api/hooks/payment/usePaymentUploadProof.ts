import { AxiosInstance } from "axios"

import { useAxios } from "hooks/useApi"
import useMutation from "hooks/useMutation"

import defaultMutationErrorHandler from "api/errors/defaultMutationError"

import { formDataIncludePicture } from "helper/pictures"

type OrderPaymentUploadProofMutationData = {
  imageUrl: string
  paymentId: number
}

export default () => {
  const axios = useAxios()

  return useMutation<any, OrderPaymentUploadProofMutationData>(
    (prop) => {
      return orderPaymentUploadProofMutation(axios, prop)
    },
    {
      chainSettle: (x) =>
        x
          .then(() => {
            toast("Payment proof berhasil diupload")
          })
          .catch(defaultMutationErrorHandler({})),
    },
  )
}

export const orderPaymentUploadProofMutation = (
  axios: AxiosInstance,
  { imageUrl, paymentId }: OrderPaymentUploadProofMutationData,
) => {
  const formData = new FormData()
  formDataIncludePicture(formData, imageUrl)

  return axios
    .post(`payments/${paymentId}/proof`, formData, {
      headers: {
        Accept: "application/json",
        "Content-Type": "multipart/form-data",
      },
    })
    .then((res) => {
      return res.data
    })
}
