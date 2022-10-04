import React, { useEffect, useMemo, useState } from "react"
import CurrencyInput from "react-native-currency-input"
import { Button, Div } from "react-native-magnus"

import EndOfList from "components/CommonList/EndOfList"
import FooterLoading from "components/CommonList/FooterLoading"
import { Select } from "components/Select"
import Text from "components/Text"

import useMultipleQueries from "hooks/useMultipleQueries"

import useBrandList from "api/hooks/pos/productCategorization/useBrandList"
import useUserLoggedInData from "api/hooks/user/useUserLoggedInData"

import { dataFromPaginated } from "helper/pagination"
import s, { COLOR_PLACEHOLDER, COLOR_DISABLED } from "helper/theme"

import { Brand } from "types/POS/ProductCategorization/Brand"

type PropTypes = {
  value: string
  onSelect: (value) => void
  disabled?: boolean
  multiple?: boolean
  status?: any
  setEstimation?: any
  profile?: any
}

export default ({
  value,
  onSelect,
  disabled,
  status,
  profile,
  multiple = false,
  setEstimation,
}: PropTypes) => {
  const [visible, setVisible] = useState(false)
  const {
    queries: [{ data: brandPaginatedData }],
    meta,
  } = useMultipleQueries([useBrandList()] as const)

  const { isError, isLoading, isFetchingNextPage, hasNextPage, fetchNextPage } =
    meta

  const data: Brand[] = dataFromPaginated(brandPaginatedData)

  const activeBrand = useMemo(
    () =>
      !!data &&
      data.length > 0 &&
      data.find((x) => x.id === parseInt(value, 10)),
    [value, data],
  )

  const renderText = () => {
    if (!!multiple) {
      if (value.length > 1) {
        return "Multiple brands selected"
      }
      if (value.length === 1 && !!activeBrand) {
        return <Text>{activeBrand.name}</Text>
      }
    }

    if (!multiple && !!activeBrand) {
      return <Text>{activeBrand.name}</Text>
    }

    return "Click to select brand(s)"
  }
  const [estimated, setEstimated] = useState(value)
  useEffect(() => {
    setEstimated(value)
  }, [value])
  const [tempArray, setTempArray] = useState([])
  useEffect(() => {
    const array1 = []
    // eslint-disable-next-line array-callback-return
    value.map(async (i, index) => {
      await array1.push({
        estimated_value: estimated[index].toString(),
        product_brand_id: value[index],
      })
      setEstimation(array1)
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [estimated])
  return (
    <>
      <Button
        block
        borderWidth={1}
        bg="white"
        color={
          (!Array.isArray(value) && value) ||
          (Array.isArray(value) && value.length > 0)
            ? "primary"
            : COLOR_PLACEHOLDER
        }
        fontSize={11}
        py={13}
        borderColor="grey"
        justifyContent="flex-start"
        onPress={() => setVisible(!visible)}
        disabled={disabled || isLoading}
      >
        {renderText()}
      </Button>
      <Select
        multiple={multiple}
        visible={visible}
        setVisible={setVisible}
        onSelect={onSelect}
        value={value}
        title="Brand List"
        message="Please select brand(s)"
        data={data || []}
        onEndReached={() => {
          hasNextPage && fetchNextPage()
        }}
        ListFooterComponent={() =>
          !!data &&
          data.length > 0 &&
          (isFetchingNextPage ? <FooterLoading /> : <EndOfList />)
        }
        keyExtractor={(item, index) => `brand_${item.id}`}
        renderItem={(item, index) => (
          <Select.Option
            value={item?.id?.toString()}
            p={20}
            borderBottomWidth={0.8}
            borderBottomColor={COLOR_DISABLED}
          >
            {item.name}
          </Select.Option>
        )}
      />
      {!!estimated && estimated.length > 0 && (
        <Div bg="#ecf0f1" mt={10} rounded={6}>
          <Text mt={5} mb={3} mx={10}>
            Estimated Value
          </Text>
          {estimated.map((i, index) => {
            const name = data.find((e) => e.id.toString() === value[index])
            return (
              <>
                <Text fontWeight="bold" p={10}>
                  {name?.name}
                </Text>
                <CurrencyInput
                  value={i}
                  returnKeyType={"done"}
                  key={index}
                  onChangeValue={(t) => {
                    setEstimated((estimated) => {
                      const newArr = [...estimated]
                      newArr[index] = t
                      return newArr
                    })
                  }}
                  prefix="Rp."
                  delimiter="."
                  separator=","
                  precision={0}
                  style={{
                    borderWidth: 1,
                    borderColor: "#c4c4c4",
                    backgroundColor: "white",
                    padding: 12,
                    borderRadius: 4,
                    marginHorizontal: 20,
                    marginBottom: 10,
                  }}
                />
                {profile.companyId === 1 &&
                profile.companyId === 1 &&
                status === "HOT" &&
                estimated[index] <= 5000000 ? (
                  <Text p={10} color="red">
                    Minimal estimated brand Rp 5.000.000
                  </Text>
                ) : null}
              </>
            )
          })}
        </Div>
      )}
    </>
  )
}
