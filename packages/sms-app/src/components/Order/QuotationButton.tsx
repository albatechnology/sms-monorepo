import * as FileSystem from "expo-file-system"
import * as Sharing from "expo-sharing"
import React, { useState } from "react"
import { Button } from "react-native-magnus"

import Loading from "components/Loading"
import Text from "components/Text"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"

import useCompanyById from "api/hooks/company/useCompanyById"
import usePaymentList from "api/hooks/payment/usePaymentList"
import useUserById from "api/hooks/user/useUserById"

import { dataFromPaginated } from "helper/pagination"

import { Order } from "types/Order"

import GenerateQuotation from "./GenerateQuotation"

type PropTypes = {
  order: Order
  isDeals: boolean
}

export default ({ order, isDeals }: PropTypes) => {
  const [loading, setLoading] = useState(false)
  const {
    queries: [
      { data: userData },
      { data: companyData },
      { data: paginatedPaymentData },
    ],
    meta: { isError, isLoading, refetch },
  } = useMultipleQueries(
    [
      useUserById(order?.userId, { enabled: !!order?.userId }),
      useCompanyById(order?.companyId, { enabled: !!order?.companyId }),
      usePaymentList({ filterOrderId: order?.id.toString(), sort: "-id" }, 99, {
        // enabled: !!isDeals,
      }),
    ] as const,
    { useStandardIsLoadingBehaviour: true },
  )
  const {
    loggedIn,
    data: { jwt },
  } = useAuth()
  const GetData = () => {
    setLoading(true)
    FileSystem.downloadAsync(
      `https://app.melandas-indonesia.com/api/v1/orders/export-quotation?type=${
        isDeals ? "invoice" : "quotation"
      }&order_id=${order.id}`,
      FileSystem.documentDirectory + `${order?.invoiceNumber}.pdf`,
      {
        headers: {
          Authorization: `Bearer ${jwt}`,
          Accept: "application/json",
        },
      },
    )
      .then(async ({ uri }) => {
        Sharing.shareAsync(uri).then((res) => {
          // console.log(res, "quotation")
        })
      })
      .catch((err) => {
        console.log(err)
      })
      .finally(() => {
        setLoading(false)
      })
  }
  if (loading === true) {
    return <Loading />
  }
  const paymentList = dataFromPaginated(paginatedPaymentData) ?? []
  return (
    <Button
      block
      bg="primary"
      mx={20}
      mb={20}
      loading={isLoading}
      alignSelf="center"
      onPress={() => {
        if (isError) {
          toast("Error: Mohon coba beberapa saat lagi.")
          refetch()
        } else {
          // GenerateQuotation(order, userData, companyData, paymentList, isDeals)
          GetData()
        }
      }}
    >
      <Text fontWeight="bold" color="white">
        {isDeals ? "Invoice" : "Quotation"}
      </Text>
    </Button>
  )
}
