import { RouteProp, useNavigation, useRoute } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import { add } from "date-fns"
import React, { useState, useEffect } from "react"
import { FlatList, TouchableOpacity } from "react-native"
import { Button, Checkbox, Div, Input } from "react-native-magnus"

import AddressSelectorInput from "components/AddressSelectorInput"
import CustomKeyboardAvoidingView from "components/CustomKeyboardAvoidingView"
import DateTimePickerInput from "components/DateTimePickerInput"
import Loading from "components/Loading"
import SelectInteriorDesign from "components/SelectInteriorDesign"
import Text from "components/Text"

import { useAxios } from "hooks/useApi"
import useMultipleQueries from "hooks/useMultipleQueries"

import { useAuth } from "providers/Auth"
import { useCart } from "providers/Cart"

import { customErrorHandler } from "api/errors"
import useActivityCheckout from "api/hooks/activity/useActivityCheckout"
import useLeadById from "api/hooks/lead/useLeadById"
import useOrderCreateMutation from "api/hooks/order/useOrderCreateMutation"
import useOrderPreview from "api/hooks/order/useOrderPreview"

import { EntryStackParamList } from "Router/EntryStackParamList"

import { formatCurrency } from "helper"
import Languages from "helper/languages"

import AddProductModal from "./AddProductModal"
import AdditionalFeeButton from "./AdditionalFeeButton"
import AdditionalFeeModal from "./AdditionalFeeModal"
import CheckoutItem from "./CheckoutItem"
import DiscountButton from "./DiscountButton"
import DiscountModal from "./DiscountModal"
import NewProduct from "./NewProduct"

type CurrentScreenRouteProp = RouteProp<EntryStackParamList, "Checkout">

type CurrentScreenNavigationProp = StackNavigationProp<EntryStackParamList>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const [discModalVisible, setDiscModalVisible] = useState(false)
  const [addFeeModalVisible, setAddFeeModalVisible] = useState(false)
  const [estimatedVisible, setEstimatedVisible] = useState(false)
  const axios = useAxios()
  const { loggedIn } = useAuth()
  const [createOrder, { isLoading: createOrderLoading }] =
    useOrderCreateMutation()

  const { filteredCartDataBySelected, removeItem, totalPrice } = useCart()
  // Go back if cart is empty
  // useEffect(() => {
  //   if (filteredCartDataBySelected.length === 0) {
  //     navigation.canGoBack() ? navigation.goBack() : navigation.navigate("Main")
  //   }
  // }, [navigation, filteredCartDataBySelected])
  const leadId = route?.params?.leadId ?? -1
  if (leadId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Main")
    }
    toast(Languages.PageNotFound)
    return null
  }
  const {
    queries: [{ data: leadData }, { data: estimatedData }],
    meta: { isError, isLoading, isFetching, refetch },
  } = useMultipleQueries([
    useLeadById(
      leadId,
      customErrorHandler({
        404: () => {
          toast("Lead tidak ditemukan")
          if (navigation.canGoBack()) {
            navigation.goBack()
          } else {
            navigation.navigate("Main")
          }
        },
      }),
    ),
    useActivityCheckout(leadId),
  ] as const)
  // const [discountId, setDiscountId] = useState(null)
  const [activeDiscount, setActiveDiscount] = useState<[]>([])
  const [discountDetail, setDiscountDetail] = useState<[]>([])
  const [shippingId, setShippingId] = useState(null)
  const [billingId, setBillingId] = useState(null)
  const [packingFee, setPackingFee] = useState<number>(0)
  const [shippingFee, setShippingFee] = useState<number>(0)
  const [interiorId, setInteriorId] = useState<number>(null)
  const [additionalDiscount, setAdditionalDiscount] = useState<number>(0)
  const [newProduct, setNewProduct] = useState([])
  const [newPrice, setNewPrice] = useState(0)
  const [index, setIndex] = useState(null)
  const [modalProduct, setModalProduct] = useState<boolean>(false),
    [productName, setProductName] = useState(""),
    [productPrice, setProductPrice] = useState<number>(0),
    [productQty, setProductQty] = useState<number>(0)
  const [expectedShippingDatetime, setExpectedShippingDatetime] =
    useState<Date>(null)
  const [expectedValidQuotation, setExpectedValidQuotation] =
    useState<Date>(null)
  const [note, setNote] = useState("")
  const [discountType, setDiscountType] = useState(0)
  const [checked, setChecked] = useState(false)
  const { data: orderPreviewData, isLoading: orderPreviewIsLoading } =
    useOrderPreview(
      {
        leadId: leadId,
        items: filteredCartDataBySelected.map((x) => ({
          id: x.productUnitId,
          quantity: x.quantity,
          is_ready: x?.is_ready,
          location_id: x?.location?.orlan_id,
        })),
        discountId: activeDiscount,
        expectedPrice: null,
        shippingAddressId: shippingId,
        billingAddressId: billingId,
        taxInvoiceId: null, // Clarification needed
        note: note,
        shippingFee: shippingFee,
        packingFee: packingFee,
        additionalDiscount: additionalDiscount,
        expectedShippingDateTime: expectedShippingDatetime,
        expectedValidQuotation: expectedValidQuotation,
        discountType: discountType,
      },
      { enabled: !!shippingId && !!billingId },
    )
  const expectedPrice =
    orderPreviewData?.totalPrice ||
    totalPrice + newPrice + shippingFee + packingFee - additionalDiscount
  const Header = (
    <>
      <AddressSelectorInput
        title="Shipping Address"
        customerId={leadData?.customer?.id}
        value={shippingId}
        onSelect={setShippingId}
      />
      <AddressSelectorInput
        mt={5}
        title="Billing Address"
        customerId={leadData?.customer?.id}
        value={billingId}
        onSelect={setBillingId}
      />
      <Div h={120} justifyContent="center">
        <SelectInteriorDesign
          value={interiorId}
          title="Interior Design List"
          message="Please select a Interior Design"
          onSelect={(value) => setInteriorId(value)}
          id={interiorId}
        />
      </Div>
      <Div
        mt={5}
        p={20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor="grey"
        row
        justifyContent="space-between"
      >
        <Text fontSize={14} fontWeight="bold">
          Product
        </Text>
      </Div>
      <Div bg="white" pb={20} />
    </>
  )
  const Footer = (
    <>
      <NewProduct
        data={newProduct}
        setIndex={setIndex}
        setVisible={setModalProduct}
        onRemove={() => {
          getProductList()
        }}
      />
      <DiscountButton
        discountDetail={discountDetail}
        activeDiscount={activeDiscount}
        setVisible={setDiscModalVisible}
        disabled={!shippingId || !billingId}
      />
      <AdditionalFeeButton
        setVisible={setAddFeeModalVisible}
        packingFee={packingFee}
        shippingFee={shippingFee}
        additionalDiscount={additionalDiscount}
      />
      <Div
        mt={5}
        p={20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor="grey"
      >
        <Text fontSize={14} fontWeight="bold">
          Order Detail
        </Text>
      </Div>
      {!!orderPreviewIsLoading ? (
        <Loading />
      ) : (
        <>
          <Div p={20} bg="white">
            <Div row justifyContent="space-between" mb={5}>
              <Text>Price</Text>
              <Text>{formatCurrency(totalPrice)}</Text>
            </Div>
            {newPrice !== 0 ? (
              <Div row justifyContent="space-between" mb={5}>
                <Text>Product Non Existing</Text>
                <Text>{formatCurrency(newPrice)}</Text>
              </Div>
            ) : null}
            {!!orderPreviewData?.totalDiscount && (
              <Div row justifyContent="space-between" mb={5}>
                <Text>Discount</Text>
                <Text>
                  {formatCurrency(-1 * orderPreviewData?.totalDiscount)}
                </Text>
              </Div>
            )}
            <Div row justifyContent="space-between" mb={5}>
              <Text>Packing</Text>
              <Text>{formatCurrency(packingFee)}</Text>
            </Div>
            <Div row justifyContent="space-between" mb={5}>
              <Text>Shipping</Text>
              <Text>{formatCurrency(shippingFee)}</Text>
            </Div>
            <Div row justifyContent="space-between">
              <Text>Additional Discount</Text>
              {discountType === 0 ? (
                <Text>{formatCurrency(-1 * additionalDiscount)}</Text>
              ) : (
                <Text>{additionalDiscount} %</Text>
              )}
            </Div>
          </Div>
          <Div p={20} bg="white" borderTopWidth={0.8} borderTopColor="grey">
            <Div row justifyContent="space-between">
              <Text fontWeight="bold">Sub Total</Text>
              <Text fontWeight="bold">{formatCurrency(expectedPrice)}</Text>
            </Div>
          </Div>
          <Div mt={5} p={20} bg="white">
            <Div
              row
              borderBottomWidth={0.8}
              borderBottomColor="grey"
              mb={20}
              justifyContent="space-between"
            >
              <Text fontSize={13} mb={20} fontWeight="bold">
                Direct Purchase
              </Text>
              <Checkbox
                checked={checked}
                onChange={() => setChecked(!checked)}
              />
            </Div>

            <Div row justifyContent="space-between">
              <Text fontSize={13} fontWeight="bold">
                Estimated by Brand
              </Text>
              <TouchableOpacity
                onPress={() => setEstimatedVisible(!estimatedVisible)}
              >
                <Text color="#17949D" fontSize={14}>
                  {estimatedVisible === true ? "Hide" : "Show"}
                </Text>
              </TouchableOpacity>
            </Div>
            {estimatedVisible === true ? (
              <Div p={3} mt={10} bg="white">
                <FlatList
                  keyExtractor={(_, idx: number) => idx.toString()}
                  data={estimatedData}
                  renderItem={({ item }) => (
                    <Div row justifyContent="space-between" mb={5}>
                      <Text>{item.product_brand} </Text>
                      <Text>{item.estimated_value}</Text>
                    </Div>
                  )}
                />
              </Div>
            ) : null}
          </Div>
          <Div
            mt={5}
            p={20}
            bg="white"
            borderBottomWidth={0.8}
            borderBottomColor="grey"
          >
            <Text fontSize={14} fontWeight="bold">
              Valid Quotation Date
            </Text>
          </Div>

          <Div p={20} bg="white">
            <DateTimePickerInput
              placeholder="Select Valid Quotation Date"
              value={expectedValidQuotation}
              onSelect={(date) => setExpectedValidQuotation(date)}
              minimumDate={new Date()}
              maximumDate={add(new Date(), { months: 12 })}
              pickTime={false}
            />
          </Div>
          {/* pembatas */}
          <Div
            mt={5}
            p={20}
            bg="white"
            borderBottomWidth={0.8}
            borderBottomColor="grey"
          >
            <Text fontSize={14} fontWeight="bold">
              Expected Delivery Date
            </Text>
          </Div>
          <Div p={20} bg="white">
            <DateTimePickerInput
              placeholder="Select expected delivery date"
              value={expectedShippingDatetime}
              onSelect={(date) => setExpectedShippingDatetime(date)}
              minimumDate={new Date()}
              maximumDate={add(new Date(), { months: 12 })}
              pickTime={false}
            />
          </Div>
        </>
      )}

      <Div
        mt={5}
        p={20}
        bg="white"
        borderBottomWidth={0.8}
        borderBottomColor="grey"
      >
        <Text fontSize={14} fontWeight="bold">
          Note (optional)
        </Text>
      </Div>
      <Div px={20} py={10} bg="white">
        <Input
          placeholder="Input note here"
          placeholderTextColor="grey"
          value={note}
          onChangeText={setNote}
          multiline={true}
          borderColor="grey"
          textAlignVertical="top"
          numberOfLines={5}
          scrollEnabled={false}
        />
      </Div>

      <Div mt={5} p={20} bg="white">
        <Button
          onPress={() => {
            createOrder(
              {
                leadId: leadId,
                items: filteredCartDataBySelected.map((x) => ({
                  id: x.productUnitId,
                  quantity: x.quantity,
                  is_ready: x?.is_ready,
                  location_id: x?.location?.orlan_id,
                })),
                discountId: activeDiscount,
                expectedPrice: expectedPrice,
                shippingAddressId: shippingId,
                billingAddressId: billingId,
                taxInvoiceId: null, // Clarification needed
                note: note,
                shippingFee: shippingFee,
                packingFee: packingFee,
                interiorDesignId: interiorId,
                additionalDiscount: additionalDiscount,
                expectedShippingDatetime: expectedShippingDatetime,
                expectedValidQuotation: expectedValidQuotation,
                discountType: discountType,
                isDirectorPurchase: checked,
              },
              (x) =>
                x.then((res) => {
                  navigation.reset({
                    index: 0,
                    routes: [{ name: "Main" }],
                  })
                  filteredCartDataBySelected.map((x) =>
                    removeItem(x.productUnitId),
                  )
                }),
            )
          }}
          bg="primary"
          alignSelf="center"
          w={"100%"}
          loading={createOrderLoading}
          disabled={
            !leadId ||
            !shippingId ||
            !billingId ||
            // !totalPrice ||
            !expectedShippingDatetime ||
            !expectedValidQuotation
            // ||
            // filteredCartDataBySelected.length === 0
          }
        >
          <Text fontWeight="bold" color="white">
            Place Order
          </Text>
        </Button>
      </Div>
    </>
  )
  const getProductList = () => {
    axios
      .get(`cart-demands`, {
        headers: {
          loggedIn,
        },
      })
      .then((res) => {
        setNewProduct(res.data.data.items)
        setNewPrice(res.data.data.total_price)
      })
      .catch((err) => {
        if (err) {
          console.log(err)
        }
      })
  }
  useEffect(() => {
    getProductList()
  }, [])
  return (
    <CustomKeyboardAvoidingView style={{ flex: 1 }}>
      <DiscountModal
        activeDiscount={activeDiscount}
        visible={discModalVisible}
        setVisible={setDiscModalVisible}
        setActiveDiscount={setActiveDiscount}
        setDiscountDetail={setDiscountDetail}
        discountDetail={discountDetail}
      />

      <AdditionalFeeModal
        visible={addFeeModalVisible}
        setVisible={setAddFeeModalVisible}
        packingFee={packingFee}
        shippingFee={shippingFee}
        additionalDiscount={additionalDiscount}
        setPackingFee={setPackingFee}
        setShippingFee={setShippingFee}
        setAdditionalDiscount={setAdditionalDiscount}
        setDiscountType={setDiscountType}
      />
      <AddProductModal
        visible={modalProduct}
        setVisible={setModalProduct}
        productName={productName}
        setProductName={setProductName}
        productQty={productQty}
        setProductQty={setProductQty}
        additionalDiscount={productPrice}
        setAdditionalDiscount={setProductPrice}
        onClick={getProductList}
      />
      <FlatList
        keyboardShouldPersistTaps="always"
        contentContainerStyle={{ flexGrow: 1 }}
        data={filteredCartDataBySelected || []}
        keyExtractor={(item, index) => `cart_item_${index}`}
        showsVerticalScrollIndicator={false}
        bounces={false}
        ListHeaderComponent={Header}
        ListFooterComponent={Footer}
        renderItem={({ item, index }) => (
          <CheckoutItem
            item={item}
            index={index}
            onRemove={(id) => removeItem(id)}
          />
        )}
      />
    </CustomKeyboardAvoidingView>
  )
}
