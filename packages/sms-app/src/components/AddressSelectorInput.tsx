import { useNavigation } from "@react-navigation/native"
import React, { useMemo, useState } from "react"
import { Pressable } from "react-native"
import { Button, Div, DivProps, Icon } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import Loading from "components/Loading"
import { Select } from "components/Select"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import { AddressType } from "api/generated/enums"
import useAddressDeleteMutation from "api/hooks/address/useAddressDeleteMutation"
import useAddressList from "api/hooks/address/useAddressList"

import { dataFromPaginated } from "helper/pagination"
import { COLOR_PLACEHOLDER } from "helper/theme"

import { Address } from "types/Address"

import AddressView from "./AddressView"

type PropTypes = DivProps & {
  customerId: number
  value: string
  onSelect: (value) => void
  disabled?: boolean
  title?: string
  type?: AddressType
}

export default ({
  customerId,
  value,
  onSelect,
  disabled = false,
  title = "Address",
  ...rest
}: PropTypes) => {
  const navigation = useNavigation()

  const [deleteAddress] = useAddressDeleteMutation()

  const [visible, setVisible] = useState(false)

  const {
    queries: [{ data: addressPaginatedData }],
    meta,
  } = useMultipleQueries([
    useAddressList(customerId, {}, 10, { enabled: !!customerId }),
  ] as const)

  const { isError, isLoading, hasNextPage, fetchNextPage, isFetchingNextPage } =
    meta

  const data: Address[] = dataFromPaginated(addressPaginatedData)

  const activeAddress = useMemo(
    () =>
      !!data &&
      data.length > 0 &&
      data.find((x) => x.id === parseInt(value, 10)),
    [value, data],
  )

  if (isLoading) {
    return <Loading />
  }

  return (
    <Div {...rest}>
      <Div bg="white" p={20} borderBottomWidth={0.8} borderBottomColor="grey">
        <Text fontSize={14} fontWeight="bold">
          {title}
        </Text>
      </Div>
      <Button
        flex={1}
        block
        borderWidth={0}
        bg="white"
        color={value ? "primary" : COLOR_PLACEHOLDER}
        fontSize={11}
        p={20}
        justifyContent="flex-start"
        onPress={() => setVisible(true)}
        disabled={disabled}
      >
        {!!activeAddress ? (
          <Div>
            <AddressView address={activeAddress} />
          </Div>
        ) : (
          "Click to select an address"
        )}
      </Button>
      <Select
        visible={visible}
        setVisible={setVisible}
        title={
          <Title
            onAddNewAddress={() => {
              setVisible(false)
              navigation.navigate("AddAddress", { customerId })
            }}
          />
        }
        message={"Please select an address"}
        onSelect={onSelect}
        value={!!value ? value.toString() : null}
        data={data}
        onEndReached={() => {
          hasNextPage && fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `address_${item.id}`}
        renderItem={(item, index) => (
          <Select.Option
            value={item.id.toString()}
            py={20}
            borderBottomWidth={0.8}
            borderBottomColor="grey"
          >
            <Div row justifyContent="space-between" alignItems="flex-start">
              <Div maxW={"65%"}>
                <AddressView address={item} />
              </Div>
              <Div row>
                <Pressable
                  onPress={() => {
                    deleteAddress({ id: item.id })
                  }}
                >
                  <Icon
                    rounded="circle"
                    bg="primary"
                    p={10}
                    name="trash"
                    color="white"
                    fontSize={16}
                    fontFamily="Ionicons"
                    mr={5}
                  />
                </Pressable>
                <Pressable
                  onPress={() => {
                    setVisible(false)
                    navigation.navigate("EditAddress", {
                      addressId: item.id,
                    })
                  }}
                >
                  <Icon
                    rounded="circle"
                    bg="primary"
                    p={10}
                    name="edit"
                    color="white"
                    fontSize={16}
                    fontFamily="FontAwesome5"
                  />
                </Pressable>
              </Div>
            </Div>
          </Select.Option>
        )}
      />
    </Div>
  )
}

const Title = ({ onAddNewAddress }) => {
  return (
    <Div row justifyContent="space-between" mb={10}>
      <Text fontSize={14} fontWeight="bold">
        ADDRESS
      </Text>
      <Pressable onPress={onAddNewAddress}>
        <Text fontSize={14}>+ Add address</Text>
      </Pressable>
    </Div>
  )
}
