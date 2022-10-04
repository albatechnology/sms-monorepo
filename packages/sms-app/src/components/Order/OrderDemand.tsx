import { useNavigation } from "@react-navigation/native"
import React, { useEffect, useState } from "react"
import { FlatList, TouchableOpacity, useWindowDimensions } from "react-native"
import Spinner from "react-native-loading-spinner-overlay"
import { Button, Div, Modal, Overlay } from "react-native-magnus"
import { useMutation, useQuery } from "react-query"

import Image from "components/Image"
import Text from "components/Text"
import UploadPicture from "components/UploadPicture"

import { useAxios } from "hooks/useApi"
import useMutationComponent from "hooks/useMutation"

import { useAuth } from "providers/Auth"

import { formatCurrency, formatDateOnly } from "helper"
import { formDataIncludePicture } from "helper/pictures"
import { COLOR_DISABLED } from "helper/theme"

import {
  Order,
  orderApprovalStatusConfig,
  orderPaymentStatusConfig,
} from "types/Order"

import OrderPaymentDetail from "./OrderPaymentDetail"
import QuotationButton from "./QuotationButton"

type PropTypes = {
  orderData: Order
  isDeals: boolean
  showQuotation?: boolean
  manualRefetch?: any
}

export default function OrderDetail({
  orderData,
  isDeals,
  showQuotation = true,
}: PropTypes) {
  const navigation = useNavigation()
  const { width: screenWidth } = useWindowDimensions()
  const [image, setImage] = useState(null)
  const [overlayVisible, setOverlayVisible] = useState(false)
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [index, setIndex] = useState("")
  const [spinner, setSpinner] = useState<boolean>(false)
  const handleSubmit = () => {
    setSpinner(true)
    var formData = new FormData()
    var imageUrl = image?.uri
    formDataIncludePicture(formData, imageUrl)
    axios
      .post(`order-details/${index}/upload`, formData, {
        headers: {
          loggedIn,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((res) => {
        console.log(res)
      })
      .catch((err) => {
        if (err) {
          console.log(err)
        }
      })
      .finally(() => {
        setSpinner(false)
      })
  }
  return (
    <FlatList
      data={orderData.orderDetails}
      keyExtractor={({ id }) => `comment${id}`}
      showsVerticalScrollIndicator={false}
      bounces={false}
      ListHeaderComponent={
        <Text px={20} mb={10}>
          Order Details
        </Text>
      }
      ListFooterComponent={
        <>
          <Div px={20} mb={10} row justifyContent="space-between">
            <Text>Packing Fee:</Text>
            <Text>{formatCurrency(orderData.packingFee)}</Text>
          </Div>
          <Div px={20} mb={10} row justifyContent="space-between">
            <Text>Shipping Fee:</Text>
            <Text>{formatCurrency(orderData.shippingFee)}</Text>
          </Div>
          <Div px={20} mb={10} row justifyContent="space-between">
            <Text>Additional Discount:</Text>
            <Text>{formatCurrency(orderData.additionalDiscount)}</Text>
          </Div>
          <Div px={20} mb={10} row justifyContent="space-between">
            <Text>Expected Payment:</Text>
            <Text>{formatCurrency(orderData.totalPrice)}</Text>
          </Div>
          <Div px={20} mb={10} row justifyContent="space-between">
            <Text>Expected Delivery Date:</Text>
            <Text>{formatDateOnly(orderData.expectedShippingDate)}</Text>
          </Div>
          {!!orderData?.note && (
            <Div px={20} mb={10}>
              <Text mb={10}>Order Note</Text>
              <Div p={10} borderColor={COLOR_DISABLED} borderWidth={0.8}>
                <Text>{orderData.note}</Text>
              </Div>
            </Div>
          )}

          {!!orderData?.approvedBy && (
            <Div px={20} mb={10} row justifyContent="space-between">
              <Text>Approved By:</Text>
              <Text>{orderData.approvedBy.name}</Text>
            </Div>
          )}

          {orderData.approvalStatus !== "WAITING_APPROVAL" ? (
            <>
              <Div px={20} mb={10} row justifyContent="space-between">
                <Text>Payment Status:</Text>
                <Text
                  color={
                    orderPaymentStatusConfig[orderData.paymentStatus].textColor
                  }
                >
                  {
                    orderPaymentStatusConfig[orderData.paymentStatus]
                      .displayText
                  }
                </Text>
              </Div>
              <OrderPaymentDetail orderData={orderData} />
              {orderPaymentStatusConfig[orderData?.paymentStatus]
                .needPayment && (
                <Button
                  block
                  bg="primary"
                  mx={20}
                  mb={10}
                  alignSelf="center"
                  onPress={() =>
                    navigation.navigate("Payment", {
                      orderId: orderData?.id,
                    })
                  }
                >
                  <Text fontWeight="bold" color="white">
                    Add Payment
                  </Text>
                </Button>
              )}
            </>
          ) : (
            <>
              <Div px={20} mb={10} row justifyContent="space-between">
                <Text>Approval Status:</Text>
                <Div
                  p={5}
                  bg={orderApprovalStatusConfig[orderData.approvalStatus].bg}
                >
                  <Text
                    color={
                      orderApprovalStatusConfig[orderData.approvalStatus]
                        .textColor
                    }
                  >
                    {
                      orderApprovalStatusConfig[orderData.approvalStatus]
                        .displayText
                    }
                  </Text>
                </Div>
              </Div>
            </>
          )}
          {showQuotation && (
            <QuotationButton isDeals={isDeals} order={orderData} />
          )}
        </>
      }
      renderItem={({ item, index }) => (
        <Div flex={1} pb={10} px={20} row alignItems="center">
          <TouchableOpacity
            onPress={() => {
              setIndex(item?.id)
              setOverlayVisible(true)
            }}
          >
            <Spinner
              visible={spinner}
              textContent={"Uploading..."}
              textStyle={{
                color: "#FFF",
              }}
            />
            <Image
              width={0.3 * screenWidth}
              scalable
              source={{
                uri:
                  item?.photo?.length > 0
                    ? item?.photo[0].preview
                    : item?.images?.length > 0
                    ? item?.images[0]?.preview
                    : null,
              }}
            />
            <Div bg="#c4c4c4" top={-25}>
              <Text textAlign="center">Change</Text>
            </Div>
          </TouchableOpacity>
          <Div flex={1} ml={10}>
            <Text fontWeight="bold" mb={5}>
              {item.productUnit.name}
            </Text>
            <Text mb={5}>{item.brand.name}</Text>
            <Text mb={5}>{item.model.name}</Text>
            <Text>
              {formatCurrency(item.unitPrice)} x{item.quantity}{" "}
            </Text>
            {/* {!!item.productUnit && !!item.productUnit.production_cost && (
              <Text>
                Modal: {formatCurrency(item.productUnit.production_cost)}
              </Text>
            )} */}
          </Div>
          <Overlay
            visible={overlayVisible}
            h={450}
            w={300}
            onBackdropPress={() => setOverlayVisible(false)}
          >
            <UploadPicture
              isOrder={true}
              value={image}
              setValue={setImage}
              text="Upload Photo"
            />
            <Button
              bg="#17949D"
              alignSelf="center"
              mt={50}
              onPress={() => {
                handleSubmit()
                setOverlayVisible(false)
              }}
            >
              Upload Photo Product
            </Button>
          </Overlay>
        </Div>
      )}
    />
  )
}
