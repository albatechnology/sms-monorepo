import { useNavigation } from "@react-navigation/native"
import { LinearGradient } from "expo-linear-gradient"
import React, { useCallback, useEffect, useState } from "react"
import { FlatList, TouchableOpacity, useWindowDimensions } from "react-native"
import CurrencyInput from "react-native-currency-input"
import Spinner from "react-native-loading-spinner-overlay"
import { Button, Checkbox, Div, Modal, Overlay } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"
import { useMutation, useQuery } from "react-query"

import DiscountButton from "containers/Checkout/DiscountButton"
import DiscountModal from "containers/Checkout/DiscountModal"

import Image from "components/Image"
import Text from "components/Text"
import UploadPicture from "components/UploadPicture"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"
import useMutationComponent from "hooks/useMutation"

import { useAuth } from "providers/Auth"
import { useCart } from "providers/Cart"

import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { formatCurrency, formatDateOnly } from "helper"
import { formDataIncludePicture } from "helper/pictures"
import { COLOR_DISABLED } from "helper/theme"

import { Discount } from "types/Discount"
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
  refetch?: any
}

export default function OrderDetail({
  orderData,
  isDeals,
  showQuotation = true,
  refetch,
}: PropTypes) {
  const navigation = useNavigation()
  const { width: screenWidth } = useWindowDimensions()
  const [image, setImage] = useState(null)
  const [overlayVisible, setOverlayVisible] = useState(false)
  const [overlayProduct, setOverlayProduct] = useState(false)
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [index, setIndex] = useState("")
  const [idNewProduct, setIdNewProduct] = useState("")
  const [spinner, setSpinner] = useState<boolean>(false)
  const [editModalShown, setEditModalShown] = useState(false)
  const [discModalVisible, setDiscModalVisible] = useState(false)
  const [discountDetail, setDiscountDetail] = useState<[]>([])
  const [activeDiscount, setActiveDiscount] = useState<[]>([])
  const [shipping, setShipping] = useState<number>(0)
  const onHideModal = () => {
    setEditModalShown(false)
    setToggle(0)
  }
  const { addItem, resetCart } = useCart()
  const [modalCancel, setModalCancel] = useState(false)
  const [toggle, setToggle] = useState(0)
  const {
    queries: [{ data: userData }],
    meta: {
      isError,
      isLoading,
      isFetching,
      isManualRefetching,
      manualRefetch,
      isFetchingNextPage,
      hasNextPage,
      fetchNextPage,
    },
  } = useMultipleQueries([useUserLoggedInData()] as const)
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
  const [preview, setPreviewData] = useState({})
  const updatePreview = () => {
    axios
      .post(
        `orders/preview-update/${orderData.id}`,
        {
          additional_discount: shipping,
          discount_ids: activeDiscount,
          discount_type: toggle,
        },
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        setPreviewData(res.data.data)
      })
      .catch((err) => {
        if (err) {
          console.log(err)
        }
      })
  }

  const cancelOrder = () => {
    axios
      .put(
        `orders/cancel/${orderData.id}`,
        {},
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        console.log(res)
      })
      .catch((err) => {
        if (err) {
          console.log(err)
        }
      })
      .finally(() => {
        setModalCancel(false)
        toast("Order activity has been cancelled")
        navigation.goBack()
      })
  }
  // console.log(orderData)
  const onAddToCart = useCallback(() => {
    addItem(
      orderData.orderDetails.map((selection) => ({
        productUnitId: selection.productUnit.id,
        quantity: selection.quantity,
        productUnitData: {
          colour: selection.colour,
          covering: selection.covering,
          description: "",
          id: selection.id,
          name: selection.productUnit.name,
          price: selection.productUnit.price,
          productionCost: selection.productUnit.production_cost,
        },
      })),
    )
    navigation.navigate("Cart")
  }, [addItem, navigation, orderData])
  const cancelReverseOrder = () => {
    resetCart()
    axios
      .put(
        `orders/cancel/${orderData.id}`,
        {},
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        onAddToCart()
      })
      .catch((err) => {
        if (err) {
          console.log(err)
        }
      })
      .finally(() => {
        setModalCancel(false)
        toast("Order activity has been updated")
      })
  }
  useEffect(() => {
    updatePreview()
  }, [shipping, activeDiscount])
  const updateDiscount = () => {
    setSpinner(true)
    axios
      .put(
        `orders/${orderData.id}`,
        {
          additional_discount: shipping,
          discount_ids: activeDiscount,
          discount_type: toggle,
        },
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        toast("Discount has been updated")
      })
      .catch((err) => {
        if (err) {
          console.log(err)
        }
      })
      .finally(() => {
        setSpinner(false)
        setEditModalShown(false)
        refetch()
        setShipping(0)
        setActiveDiscount([])
      })
  }
  const handleUploadImage = () => {
    setSpinner(true)
    var formData = new FormData()
    var imageUrl = image?.uri
    formData.append("item_id", idNewProduct)
    formDataIncludePicture(formData, imageUrl)
    axios
      .post(`cart-demands/${orderData?.cartDemand?.id}/upload`, formData, {
        headers: {
          loggedIn,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((res) => {
        toast("Image has been uploaded")
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
    <>
      <Div h={10} />
      {orderData.cartDemand === null ? null : (
        <FlatList
          data={orderData?.cartDemand?.items}
          keyExtractor={(_, idx: number) => idx.toString()}
          ListHeaderComponent={
            <Text px={20} mb={10}>
              New Product
            </Text>
          }
          renderItem={({ item }) => (
            <Div flex={1} pb={10} px={20} row alignItems="center">
              <TouchableOpacity
                onPress={() => {
                  setIdNewProduct(item?.id)
                  setOverlayProduct(true)
                }}
              >
                <Image
                  width={0.3 * screenWidth}
                  scalable
                  source={{
                    uri: item?.image,
                  }}
                />
                <Div bg="#c4c4c4" top={-30}>
                  <Text textAlign="center">Change</Text>
                </Div>
              </TouchableOpacity>
              <Div flex={1} ml={10}>
                <Text fontWeight="bold" mb={5}>
                  {item?.name}
                </Text>
                <Text>
                  {formatCurrency(item?.price)} x{item.quantity}{" "}
                </Text>
              </Div>
            </Div>
          )}
        />
      )}
      <FlatList
        data={orderData.orderDetails}
        keyExtractor={({ id }) => `comment${id}`}
        listKey={(_item: any, index: { toString: () => any }) =>
          `_key${index.toString()}`
        }
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListHeaderComponent={
          <Text px={20} mb={10}>
            Order Details
          </Text>
        }
        ListFooterComponent={
          <>
            {/* {userData.type === "DIRECTOR" && userData.app_show_hpp === true ? (
              <Div px={8} mx={15} mb={10} row justifyContent="space-between">
                <Text color="#17949D" fontWeight="bold">
                  Total Hpp:
                </Text>
                <Text color="#17949D" fontWeight="bold">
                  {formatCurrency(
                    orderData.orderDetails.reduce(
                      (
                        n: any,
                        val: { productUnit: { production_cost: any } },
                      ) => val.productUnit.production_cost + n,
                      0,
                    ),
                  )}
                </Text>
              </Div>
            ) : null} */}
            <Div px={20} mb={10} row justifyContent="space-between">
              <Text>Packing Fee:</Text>
              <Text>{formatCurrency(orderData.packingFee)}</Text>
            </Div>
            <Div px={20} mb={10} row justifyContent="space-between">
              <Text>Shipping Fee:</Text>
              <Text>{formatCurrency(orderData.shippingFee)}</Text>
            </Div>
            <Div px={20} mb={10} row justifyContent="space-between">
              <Text>Discount:</Text>
              <Text>{formatCurrency(orderData.totalDiscount)}</Text>
            </Div>
            {orderData.discount === null ? null : (
              <Div px={20} mb={10} row justifyContent="space-between">
                <Text>Discount Name:</Text>
                <Text textAlign="right" w={widthPercentageToDP(40)}>
                  {orderData.discount.name}
                </Text>
              </Div>
            )}
            <Div px={20} mb={10} row justifyContent="space-between">
              <Text>Sub total:</Text>
              <Text>
                {formatCurrency(
                  orderData.originalPrice - orderData.totalDiscount,
                )}
              </Text>
            </Div>
            <Div px={20} mb={10} row justifyContent="space-between">
              {userData.type === "DIRECTOR" ? (
                <Text>Discount Request:</Text>
              ) : (
                <Text>Additional Discount:</Text>
              )}
              <Text>
                {formatCurrency(orderData.additionalDiscount)} (
                {orderData.additional_discount_ratio}%)
              </Text>
            </Div>
            <Div
              borderBottomWidth={1}
              mx={widthPercentageToDP(6)}
              mb={10}
              borderColor="#c4c4c4"
            />
            <Div px={20} mb={10} row justifyContent="space-between">
              <Text>Total Price:</Text>
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
            <Div
              borderBottomWidth={1}
              mx={widthPercentageToDP(6)}
              mb={10}
              borderColor="#c4c4c4"
            />
            {userData.type === "DIRECTOR" ? (
              <>
                <Div px={20} mb={10} row justifyContent="space-between">
                  <Text>Request by :</Text>
                  <Text>{orderData.discountTakeoverBy?.name}</Text>
                </Div>
                <Div px={20} mb={10} row justifyContent="space-between">
                  <Text>Channel Name:</Text>
                  <Text>{orderData.discountTakeoverBy?.channel}</Text>
                </Div>
                {!!orderData?.approvalNote && (
                  <Div px={20} mb={10}>
                    <Text mb={10}>Approval Note by BUM</Text>
                    <Div p={10} borderColor={COLOR_DISABLED} borderWidth={0.8}>
                      <Text>{orderData.approvalNote}</Text>
                    </Div>
                  </Div>
                )}
              </>
            ) : null}
            {!!orderData?.approvedBy && (
              <Div px={20} mb={10} row justifyContent="space-between">
                {orderData.approvalStatus === "REJECTED" ? (
                  <Text>Rejected By:</Text>
                ) : (
                  <Text>Approved By:</Text>
                )}
                <Text>{orderData.approvedBy.name}</Text>
              </Div>
            )}

            {orderData.approvalStatus !== "WAITING_APPROVAL" ? (
              <>
                <Div px={20} mb={10} row justifyContent="space-between">
                  <Text>Payment Status:</Text>
                  <Text
                    color={
                      orderPaymentStatusConfig[orderData.paymentStatus]
                        .textColor
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
                    disabled={
                      orderData.approvalStatus === "NOT_REQUIRED" ||
                      orderData.approvalStatus === "APPROVED"
                        ? false
                        : true
                    }
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
                  <Text>Status:</Text>
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
              <>
                <QuotationButton isDeals={isDeals} order={orderData} />
              </>
            )}

            {orderData.paymentStatus !== "NONE" ? null : userData.type ===
              "SALES" ? (
              <Div row justifyContent="space-between" mx={20}>
                <Button
                  block
                  bg="white"
                  borderWidth={1}
                  w={widthPercentageToDP(41)}
                  mb={10}
                  onPress={() => setEditModalShown(true)}
                  alignSelf="center"
                >
                  <Text fontWeight="bold" color="black">
                    Edit Discount
                  </Text>
                </Button>
                <Button
                  block
                  bg="white"
                  borderWidth={1}
                  borderColor="#d63031"
                  mb={10}
                  w={widthPercentageToDP(41)}
                  onPress={() => setModalCancel(true)}
                  alignSelf="center"
                >
                  <Text fontWeight="bold" color="#d63031">
                    Cancel Order
                  </Text>
                </Button>
              </Div>
            ) : null}
            <Overlay
              visible={modalCancel}
              p="xl"
              onBackdropPress={() => setModalCancel(false)}
            >
              <Text>Are you sure to cancel this order ?</Text>
              <Div mt={10} row justifyContent="space-around">
                <Button flex={3} onPress={cancelOrder} bg="black">
                  Cancel Order
                </Button>
                <Button
                  mx={5}
                  flex={3}
                  onPress={cancelReverseOrder}
                  bg="white"
                  color="black"
                  borderWidth={1}
                >
                  Edit Order
                </Button>
              </Div>
            </Overlay>
            <Modal
              useNativeDriver
              isVisible={editModalShown}
              animationIn="slideInUp"
              animationOut="slideOutDown"
              onBackdropPress={onHideModal}
              onModalHide={onHideModal}
              onBackButtonPress={onHideModal}
              h="80%"
            >
              <Div h="100%" px={20} pt={20}>
                <Text
                  fontSize={14}
                  ml={widthPercentageToDP(5)}
                  fontWeight="bold"
                >
                  Discount:
                </Text>
                <DiscountButton
                  discountDetail={discountDetail}
                  activeDiscount={activeDiscount}
                  setVisible={setDiscModalVisible}
                  disabled={false}
                />
                {/* <DiscountModal
                  visible={discModalVisible}
                  setVisible={setDiscModalVisible}
                  setActiveDiscount={setActiveDiscount}
                /> */}
                <DiscountModal
                  activeDiscount={activeDiscount}
                  visible={discModalVisible}
                  setVisible={setDiscModalVisible}
                  setActiveDiscount={setActiveDiscount}
                  setDiscountDetail={setDiscountDetail}
                  discountDetail={discountDetail}
                />

                <Text
                  fontSize={14}
                  ml={widthPercentageToDP(5)}
                  fontWeight="bold"
                  mb={10}
                  mt={20}
                >
                  Additional Discount:
                </Text>
                <Checkbox
                  value={toggle === 0 ? 1 : 0}
                  defaultChecked={false}
                  onChange={(value) => {
                    setToggle(value)
                    setShipping(0)
                  }}
                  suffix={<Text>Discount in Percent</Text>}
                  my={heightPercentageToDP(1)}
                  mx={widthPercentageToDP(6)}
                />
                {toggle === 0 ? (
                  <CurrencyInput
                    value={shipping}
                    returnKeyType={"done"}
                    onChangeValue={(val) =>
                      !!val ? setShipping(val) : setShipping(0)
                    }
                    prefix="Rp."
                    delimiter="."
                    separator=","
                    precision={0}
                    style={{
                      borderWidth: 1,
                      borderColor: "#c4c4c4",
                      padding: 12,
                      marginHorizontal: widthPercentageToDP(6),
                    }}
                  />
                ) : (
                  <CurrencyInput
                    value={shipping}
                    returnKeyType={"done"}
                    maxLength={3}
                    onChangeValue={(val) =>
                      !!val ? setShipping(val) : setShipping(0)
                    }
                    suffix={"%"}
                    precision={0}
                    style={{
                      borderWidth: 1,
                      borderColor: "#c4c4c4",
                      padding: 12,
                      marginHorizontal: widthPercentageToDP(6),
                    }}
                  />
                )}
                {!!preview && (
                  <Div h={100} mt={10}>
                    <Div px={20} mb={10} row justifyContent="space-between">
                      <Text>Expected Price:</Text>
                      <Text>{formatCurrency(preview?.total_price)}</Text>
                    </Div>
                  </Div>
                )}
                <TouchableOpacity onPress={updateDiscount}>
                  <LinearGradient
                    style={{
                      height: 40,
                      justifyContent: "center",
                      borderRadius: 4,
                      marginHorizontal: widthPercentageToDP(5),
                    }}
                    locations={[0.5, 1.0]}
                    colors={["#20B5C0", "#17949D"]}
                  >
                    <Text color="white" fontSize={14} textAlign="center">
                      Update Discount
                    </Text>
                  </LinearGradient>
                </TouchableOpacity>
              </Div>
            </Modal>
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
            <Div flex={1} ml={10} top={heightPercentageToDP(-1.5)}>
              <Text fontWeight="bold" mb={2}>
                {item.productUnit.name}
              </Text>
              <Text mb={2}>{item.brand.name}</Text>
              <Text mb={8}>{item.model.name}</Text>
              {/* {userData.type === "DIRECTOR" &&
              userData.app_show_hpp === true ? (
                <Text mb={2} fontWeight="bold" color="#17949D">
                  Hpp: {formatCurrency(item?.productUnit?.production_cost)}
                </Text>
              ) : null} */}
              <Text>
                {formatCurrency(item.unitPrice)} x{item.quantity}{" "}
              </Text>
            </Div>
          </Div>
        )}
      />
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
          Upload Photo New Product
        </Button>
      </Overlay>
      {/* for non existing product */}
      <Overlay
        visible={overlayProduct}
        h={450}
        w={300}
        onBackdropPress={() => setOverlayProduct(false)}
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
            handleUploadImage()
            setOverlayProduct(false)
          }}
        >
          Upload Photo Product
        </Button>
      </Overlay>
    </>
  )
}
