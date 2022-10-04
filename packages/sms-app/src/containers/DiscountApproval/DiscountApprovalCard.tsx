import { useNavigation } from "@react-navigation/native"
import Case from "case"
import React, { useEffect } from "react"
import { useCallback } from "react"
import { useState } from "react"
import { TextInput, TouchableOpacity, useWindowDimensions } from "react-native"
import CurrencyInput from "react-native-currency-input"
import {
  Avatar,
  Button,
  Checkbox,
  Div,
  Icon,
  Input,
  Modal,
} from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

import OrderDetail from "components/Order/OrderDetail"
import Text from "components/Text"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import useOrderApproveDiscount from "api/hooks/order/useOrderApproveDiscount"
import useOrderClone from "api/hooks/order/useOrderClone"

import { formatCurrency, formatDate, responsive } from "helper"
import s, { COLOR_DISABLED, COLOR_PRIMARY } from "helper/theme"

import { getFullName, getInitials } from "types/Customer"
import { Order } from "types/Order"
import { User } from "types/User"

type PropTypes = {
  order: Order
  modalShown: boolean
  onHideModal: () => void
  onPress?: () => void
  userData: User
}

export default ({
  order,
  userData,
  modalShown,
  onHideModal,
  onPress,
}: PropTypes) => {
  const { width: screenWidth } = useWindowDimensions()
  const { invoiceNumber, updatedAt, channel, user, customer } = order || {}
  const [approveOrder, { isLoading }] = useOrderApproveDiscount()
  const [editDiscount, { isLoading: editDiscountIsLoading }] = useOrderClone()
  const onApprove = useCallback(() => {
    approveOrder({ orderId: order.id }, (x) =>
      x.then(() => {
        onHideModal()
      }),
    )
  }, [approveOrder, order, onHideModal])
  const onEdit = useCallback(
    (additionalDiscount: number) => {
      editDiscount({ orderId: order.id, additionalDiscount }, (x) =>
        x.then(() => {
          onHideModal()
        }),
      )
    },
    [editDiscount, order, onHideModal],
  )
  return (
    <>
      <TouchableOpacity onPress={onPress}>
        <Div
          p={20}
          bg="white"
          borderBottomWidth={0.8}
          borderBottomColor={COLOR_DISABLED}
        >
          <Div row mb={10}>
            <Avatar
              bg={COLOR_DISABLED}
              color={COLOR_PRIMARY}
              size={responsive(32)}
              mr={10}
            >
              {getInitials(customer)}
            </Avatar>
            <Div
              row
              flex={1}
              justifyContent="space-between"
              alignItems="flex-start"
            >
              <Div
                flex={1}
                row
                justifyContent="space-between"
                alignItems="flex-start"
              >
                <Div maxW={0.5 * screenWidth}>
                  <Text fontSize={10} color={COLOR_DISABLED} mb={5}>
                    {invoiceNumber}
                  </Text>
                  <Text mb={5}>{formatDate(updatedAt)}</Text>
                  <Text fontSize={14} fontWeight="bold" mb={5}>
                    {getFullName(customer)}
                  </Text>
                </Div>
                {!!channel && (
                  <Div
                    borderWidth={2}
                    borderColor="primary"
                    py={5}
                    px={10}
                    maxW={0.35 * screenWidth}
                  >
                    <Text fontWeight="bold" textAlign="center">
                      {Case.title(channel.name)}
                    </Text>
                  </Div>
                )}
              </Div>
            </Div>
          </Div>
          <Div row alignSelf="flex-end" alignItems="center">
            <Icon
              color="grey"
              name="person"
              fontSize={12}
              fontFamily="Ionicons"
              mr={5}
            />
            <Text color="grey">{user.name}</Text>
          </Div>
        </Div>
      </TouchableOpacity>
      <Modal
        useNativeDriver
        isVisible={modalShown}
        animationIn="slideInUp"
        animationOut="slideOutDown"
        onBackdropPress={onHideModal}
        onModalHide={onHideModal}
        onBackButtonPress={onHideModal}
        h="80%"
      >
        <ApprovalSection
          order={order}
          userData={userData}
          onApprove={onApprove}
          onEdit={onEdit}
          isLoading={isLoading || editDiscountIsLoading}
        />
      </Modal>
    </>
  )
}

type ApprovalSectionPropTypes = {
  order: Order
  onApprove: () => void
  onEdit: (additionalDiscount: number) => void
  isLoading: boolean
  userData: User
}
const ApprovalSection = ({
  order,
  onApprove,
  onEdit,
  isLoading,
  userData,
}: ApprovalSectionPropTypes) => {
  const [editModalShown, setEditModalShown] = useState(false)
  const [rejectModalShown, setRejectModalShown] = useState(false)
  const [editValue, setEditValue] = useState(0)
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const navigation = useNavigation()
  const onHideModal = () => {
    setEditModalShown(false)
    setRejectModalShown(false)
    setToggle(0)
  }
  const [loading, setLoading] = useState(false)
  const [note, setNote] = useState<string>("")
  const [additionalDiscount, setAdditionalDiscount] = useState<number>(null)
  const [toggle, setToggle] = useState(0)
  var priceDiscount =
    additionalDiscount === null ? order.additionalDiscount : additionalDiscount
  const sendApproval = () => {
    setLoading(true)
    axios
      .post(
        `orders/request-approval/${order.id}`,
        {
          approval_note: note,
          additional_discount: priceDiscount,
          discount_take_over_by: userData.id,
        },
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        navigation.goBack()
      })
      .catch((err) => {
        console.log(err)
      })
      .finally(() => {
        setLoading(false)
        toast("Discount Approval berhasil terkirim")
      })
  }
  const sendRejection = () => {
    setLoading(true)
    axios
      .put(
        `orders/approve/${order.id}`,
        {
          comment: note,
          reject: true,
        },
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        navigation.goBack()
      })
      .catch((err) => {
        console.log(err)
      })
      .finally(() => {
        setLoading(false)
        toast("Order has been rejected")
      })
  }
  const [preview, setPreview] = useState<number>(0)
  const updatePreview = () => {
    axios
      .post(
        `orders/preview-update/${order.id}`,
        {
          additional_discount: priceDiscount,
          discount_take_over_by: userData.id,
        },
        {
          headers: {
            loggedIn,
          },
        },
      )
      .then((res) => {
        setPreview(res.data.data.total_price)
      })
      .catch((err) => {
        if (err) {
          console.log(err)
        }
      })
  }
  useEffect(() => {
    updatePreview()
  }, [additionalDiscount])

  return (
    <>
      <Div h="100%" px={20} pt={20}>
        <OrderDetail orderData={order} isDeals={false} showQuotation={false} />
        <Div row justifyContent="space-between">
          <Button
            bg="white"
            ml={20}
            mr={10}
            alignSelf="center"
            onPress={() => setRejectModalShown(true)}
            loading={isLoading}
            color="black"
            flex={1}
            disabled={
              userData.type === "DIRECTOR" &&
              userData.app_approve_discount === false
                ? true
                : false
            }
            borderWidth={1}
            borderColor={COLOR_DISABLED}
          >
            <Text fontWeight="bold">Reject</Text>
          </Button>
          {userData?.supervisorTypeId === 2 ? (
            <Button
              bg="primary"
              mr={20}
              ml={10}
              alignSelf="center"
              onPress={() => setEditModalShown(true)}
              loading={isLoading}
              disabled={
                userData.type === "DIRECTOR" &&
                userData.app_approve_discount === false
                  ? true
                  : false
              }
              flex={1}
            >
              <Text fontWeight="bold" color="white">
                Edit
              </Text>
            </Button>
          ) : (
            // )
            <Button
              bg="primary"
              mr={20}
              ml={10}
              alignSelf="center"
              onPress={onApprove}
              loading={isLoading}
              disabled={
                userData.type === "DIRECTOR" &&
                userData.app_approve_discount === false
                  ? true
                  : false
              }
              flex={1}
            >
              <Text fontWeight="bold" color="white">
                Approve
              </Text>
            </Button>
          )}
        </Div>
      </Div>
      {/* edit section on bum */}
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
          <Text mb={10} fontWeight="bold">
            Edit Approval
          </Text>
          <Text color="#17949D" mb={10}>
            Disc Limit :{" "}
            {formatCurrency(
              (order.limitPercentage / 100) * order.originalPrice,
            )}
          </Text>
          <Text>Edit Discount</Text>
          <Checkbox
            value={toggle === 0 ? 1 : 0}
            defaultChecked={false}
            onChange={(value) => {
              setToggle(value)
              setAdditionalDiscount(null)
            }}
            suffix={<Text color="#c4c4c4">Discount in Percent</Text>}
            my={heightPercentageToDP(1)}
          />
          {toggle === 0 ? (
            <CurrencyInput
              value={additionalDiscount}
              placeholder={formatCurrency(order.additionalDiscount)}
              returnKeyType={"done"}
              onChangeValue={(val) => setAdditionalDiscount(val)}
              prefix="Rp."
              delimiter="."
              separator=","
              precision={0}
              style={{
                borderWidth: 0.6,
                borderColor: "#c4c4c4",
                padding: 12,
                borderRadius: 6,
                marginBottom: heightPercentageToDP(2),
              }}
            />
          ) : (
            <CurrencyInput
              value={(additionalDiscount / order.originalPrice) * 100}
              returnKeyType={"done"}
              placeholder={"%"}
              onChangeValue={(val) =>
                setAdditionalDiscount((val / 100) * order.originalPrice)
              }
              suffix="%"
              maxLength={3}
              precision={0}
              style={{
                borderWidth: 0.6,
                borderColor: "#c4c4c4",
                padding: 12,
                borderRadius: 6,
                marginBottom: heightPercentageToDP(2),
              }}
            />
          )}
          <Text>
            Notes<Text color="red">*</Text>
          </Text>
          {/* <Div
            mb={8}
            borderWidth={1}
            rounded={6}
            minH={100}
            borderColor={"#c4c4c4"}
          > */}
          <TextInput
            value={note}
            multiline
            style={{
              backgroundColor: "white",
              borderWidth: 0.6,
              borderColor: "#c4c4c4",
              width: widthPercentageToDP(90),
              alignSelf: "center",
              borderRadius: 8,
              paddingHorizontal: widthPercentageToDP(3),
              paddingVertical: widthPercentageToDP(4),
              textAlignVertical: "top",
              color: "black",
              height: 100,
            }}
            onChangeText={(value) => setNote(value)}
          />
          <Text>Total Price : {formatCurrency(preview)}</Text>
          {priceDiscount >
          (order.limitPercentage / 100) * order.originalPrice ? (
            <Button
              bg="primary"
              mx={20}
              mt={10}
              block
              alignSelf="center"
              onPress={sendApproval}
              loading={loading}
              disabled={note.length < 5 ? true : false}
            >
              <Text fontWeight="bold" color="white">
                Send Request Approval
              </Text>
            </Button>
          ) : (
            <Button
              bg="primary"
              mx={20}
              mt={10}
              block
              alignSelf="center"
              onPress={onApprove}
              loading={loading}
              disabled={false}
            >
              <Text fontWeight="bold" color="white">
                Approve
              </Text>
            </Button>
          )}
        </Div>
      </Modal>
      {/* Reject section */}
      <Modal
        useNativeDriver
        isVisible={rejectModalShown}
        animationIn="slideInUp"
        animationOut="slideOutDown"
        onBackdropPress={onHideModal}
        onModalHide={onHideModal}
        onBackButtonPress={onHideModal}
        h="60%"
      >
        <Div h="100%" px={20} pt={20}>
          <Text mb={10}>
            Lampirkan Catatan<Text color="red">*</Text>
          </Text>
          <Input value={note} onChangeText={(value) => setNote(value)} />
          <Button
            bg="primary"
            mx={20}
            mt={10}
            block
            alignSelf="center"
            onPress={sendRejection}
            loading={loading}
            disabled={note.length < 5 ? true : false}
          >
            <Text fontWeight="bold" color="white">
              Send Rejection
            </Text>
          </Button>
        </Div>
      </Modal>
    </>
  )
}
