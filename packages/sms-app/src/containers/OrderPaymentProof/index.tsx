import { BottomTabNavigationProp } from "@react-navigation/bottom-tabs"
import {
  CompositeNavigationProp,
  RouteProp,
  useNavigation,
  useRoute,
} from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useMemo, useState } from "react"
import { RefreshControl, ScrollView, useWindowDimensions } from "react-native"
import Spinner from "react-native-loading-spinner-overlay"
import { Button, Div } from "react-native-magnus"

import Error from "components/Error"
import Image from "components/Image"
import Loading from "components/Loading"
import Text from "components/Text"
import UploadPicture, { ImageResultType } from "components/UploadPicture"

import useMultipleQueries from "hooks/useMultipleQueries"

import { customErrorHandler } from "api/errors"
import usePaymentById from "api/hooks/payment/usePaymentById"
import usePaymentUploadProof from "api/hooks/payment/usePaymentUploadProof"

import { EntryStackParamList } from "Router/EntryStackParamList"
import {
  CustomerStackParamList,
  MainTabParamList,
} from "Router/MainTabParamList"

import Languages from "helper/languages"
import { COLOR_PRIMARY } from "helper/theme"

import { queryClient } from "../../query"

type CurrentScreenRouteProp = RouteProp<
  CustomerStackParamList,
  "OrderPaymentProof"
>
type CurrentScreenNavigationProp = CompositeNavigationProp<
  CompositeNavigationProp<
    StackNavigationProp<CustomerStackParamList, "OrderPaymentProof">,
    BottomTabNavigationProp<MainTabParamList>
  >,
  StackNavigationProp<EntryStackParamList>
>

export default () => {
  const route = useRoute<CurrentScreenRouteProp>()
  const navigation = useNavigation<CurrentScreenNavigationProp>()

  const { width: screenWidth } = useWindowDimensions()

  const paymentId = route?.params?.paymentId ?? -1
  if (paymentId === -1) {
    if (navigation.canGoBack()) {
      navigation.goBack()
    } else {
      navigation.navigate("Main")
    }
    toast(Languages.PageNotFound)
    return null
  }

  const {
    queries: [{ data: paymentData }],
    meta: {
      isError,
      isFetching,
      isLoading,
      refetch,
      manualRefetch,
      isManualRefetching,
    },
  } = useMultipleQueries([
    usePaymentById(
      paymentId,
      {},
      customErrorHandler({
        404: () => {
          toast("Payment tidak ditemukan")
          if (navigation.canGoBack()) {
            navigation.goBack()
          } else {
            navigation.navigate("Main")
          }
        },
      }),
    ),
  ] as const)

  const [imageToUpload, setImageToUpload] =
    useState<ImageResultType | null>(null)

  const [uploadPaymentProof, { isLoading: isMutationLoading }] =
    usePaymentUploadProof()

  const previousImages = useMemo(
    () => (paymentData ? paymentData?.proof?.map((x) => x.url) : []),
    [paymentData],
  )

  if (isError) {
    return <Error refreshing={isFetching} onRefresh={refetch} />
  }

  if (isLoading) {
    return <Loading />
  }

  return (
    <ScrollView
      style={{ flex: 1, backgroundColor: "white" }}
      refreshControl={
        <RefreshControl
          colors={[COLOR_PRIMARY]}
          tintColor={COLOR_PRIMARY}
          titleColor={COLOR_PRIMARY}
          title="Loading..."
          refreshing={isManualRefetching}
          onRefresh={manualRefetch}
        />
      }
    >
      <Spinner
        visible={isMutationLoading}
        textContent={"Uploading..."}
        textStyle={{
          color: "#FFF",
        }}
      />

      <Div bg="white" py={30} alignItems="center">
        {previousImages && previousImages.length > 0 && (
          <Div bg="white" mb={20} alignItems="center">
            <Text fontWeight="bold" fontSize={14} mb={20}>
              - Histori -
            </Text>
            {previousImages.map((image, i) => (
              <Image
                key={i}
                source={{ uri: image }}
                width={0.95 * screenWidth}
                scalable
              />
            ))}
          </Div>
        )}
        <Text fontWeight="bold" fontSize={14} textAlign="center">
          Upload bukti pembayaran dibawah:
        </Text>
        <UploadPicture
          value={imageToUpload}
          setValue={setImageToUpload}
          text="Tambah Bukti Pembayaran..."
          aspectRatio={[1, 1]}
        />
      </Div>

      <Div bg="white">
        <Button
          block
          bg="primary"
          mx={20}
          mb={10}
          alignSelf="center"
          disabled={!imageToUpload}
          onPress={() => {
            uploadPaymentProof(
              { imageUrl: imageToUpload.uri, paymentId },
              (x) =>
                x.then(() => {
                  queryClient.invalidateQueries(["payment", paymentId])
                  setImageToUpload(null)
                }),
            )
          }}
        >
          <Text fontWeight="bold" color="white">
            Upload
          </Text>
        </Button>
      </Div>
    </ScrollView>
  )
}
