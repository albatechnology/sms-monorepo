/* eslint-disable @typescript-eslint/no-unused-expressions */
import React, { useState } from "react"
import { useEffect } from "react"
import { FlatList } from "react-native"
import { Button, Div, Icon, Input, Modal } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Loading from "components/Loading"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { customErrorHandler } from "api/errors"
import useDiscountByCode from "api/hooks/discount/useDiscountByCode"
import useDiscountList from "api/hooks/discount/useDiscountList"

import { dataFromPaginated } from "helper/pagination"

import { Discount } from "types/Discount"

export default ({
  visible = false,
  setVisible,
  setActiveDiscount,
  activeDiscount,
  setDiscountDetail,
  discountDetail,
}) => {
  const [discountCode, setDiscountCode] = useState("")

  const [data, setData] = useState<Discount[]>([])
  const hideModal = () => setVisible(false)
  const {
    queries: [{ data: discountPaginatedData }],
    meta: { isLoading, isFetchingNextPage, hasNextPage, fetchNextPage },
  } = useMultipleQueries([
    useDiscountList({ filterName: discountCode }),
  ] as const)
  // DEBT: Handle this properly
  // const {
  //   meta: { refetch },
  // } = useMultipleQueries([
  //   useDiscountByCode(
  //     discountCode,
  //     { enabled: false },
  //     customErrorHandler({
  //       404: () => {
  //         setVisible(false)
  //         toast("Discount code invalid.")
  //       },
  //     }),
  //   ),
  // ] as const)

  useEffect(() => {
    setData(dataFromPaginated(discountPaginatedData))
  }, [discountPaginatedData])

  // if (isLoading) {
  //   return <Loading />
  // }

  return (
    <Modal
      useNativeDriver
      isVisible={visible}
      onBackdropPress={hideModal}
      animationIn={"slideInUp"}
      onBackButtonPress={hideModal}
      onDismiss={hideModal}
      onModalHide={hideModal}
      h="80%"
    >
      <Div shadow="sm" p={20} bg="white">
        <Text fontSize={16} fontWeight="bold">
          Discount List
        </Text>
      </Div>
      <Div row px={20} pt={20} bg="white">
        <Input
          flex={1}
          mr={5}
          placeholder={"Input Discount Name / Code Here"}
          focusBorderColor="primary"
          value={discountCode}
          onChangeText={(val) => {
            setDiscountCode(val)
          }}
        />
      </Div>
      {isLoading ? (
        <Loading />
      ) : (
        <FlatList
          data={data}
          keyExtractor={(item, index) => `discount_${index}`}
          showsVerticalScrollIndicator={false}
          bounces={false}
          onEndReachedThreshold={0.2}
          onEndReached={() => {
            if (hasNextPage) fetchNextPage()
          }}
          ListFooterComponent={() =>
            !!data &&
            data.length > 0 &&
            (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
          }
          renderItem={({ item, index }) => {
            return (
              <DiscountCard
                item={item}
                activeDiscount={activeDiscount}
                setDiscountDetail={(val) =>
                  discountDetail.find((e) => e === val) !== undefined
                    ? setDiscountDetail(
                        discountDetail.filter((e) => e !== item),
                      )
                    : setDiscountDetail(discountDetail.concat(val))
                }
                setActiveDiscount={(val) =>
                  activeDiscount.find((e) => e === val) !== undefined
                    ? setActiveDiscount(
                        activeDiscount.filter((e) => e !== item.id),
                      )
                    : setActiveDiscount(activeDiscount.concat(val))
                }
              />
            )
          }}
        />
      )}
    </Modal>
  )
}

const DiscountCard = ({
  item,
  setActiveDiscount,
  activeDiscount,
  setDiscountDetail,
}: {
  item: Discount
  activeDiscount: []
  setActiveDiscount: (val) => void
  setDiscountDetail: (val) => void
}) => {
  return (
    <Div mx={20} mt={20} rounded={8} bg="white" shadow="sm">
      <Div
        px={20}
        py={10}
        borderBottomWidth={0.8}
        borderBottomColor="grey"
        row
        justifyContent="space-between"
        alignItems="center"
      >
        <Text flex={1} fontWeight="bold">
          {item?.name}
        </Text>
        <Button
          px={20}
          bg={
            activeDiscount.find((e) => e === item.id) !== undefined
              ? "#e84118"
              : "primary"
          }
          color="white"
          onPress={() => {
            setActiveDiscount(item.id)
            setDiscountDetail(item)
          }}
        >
          {activeDiscount.find((e) => e === item.id) !== undefined
            ? "Delete"
            : "Apply"}
        </Button>
      </Div>
      <Text px={20} py={10}>
        {item?.description}
      </Text>
    </Div>
  )
}
