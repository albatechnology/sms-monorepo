import * as ImagePicker from "expo-image-picker"
import React, { Dispatch, useEffect } from "react"
import { Image, Dimensions, ImageBackground } from "react-native"
import { Button, Div } from "react-native-magnus"
import { widthPercentageToDP } from "react-native-responsive-screen"

import Text from "components/Text"

import { responsive } from "helper"

export type ImageResultType = ImagePicker.ImagePickerResult & {
  uri: string
}

type UploadPicturePropTypes = {
  value: ImageResultType | null
  setValue: Dispatch<ImageResultType | null>
  text: string
  aspectRatio?: [number, number]
  marginHorizontal?: number
  isOrder?: boolean
}

const { width } = Dimensions.get("window")

export default ({
  value,
  setValue,
  text,
  isOrder,
  aspectRatio = [1, 1],
  marginHorizontal = responsive(20),
}: UploadPicturePropTypes) => {
  useEffect(() => {
    ImagePicker.requestCameraPermissionsAsync()
    ImagePicker.requestMediaLibraryPermissionsAsync()
  }, [])

  const imageLength = width - marginHorizontal * 2
  const iconDimension = 0.2 * imageLength

  const aspectRatioDecimal = aspectRatio[1] / aspectRatio[0]

  const pickImage = async (pickerFunc) => {
    let result: ImageResultType = await pickerFunc({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: aspectRatio,
      quality: 1,
    })

    if (!result.cancelled) {
      setValue(result)
    }
  }

  return (
    <Div>
      <Div
        mt={10}
        borderColor="gray400"
        borderWidth={1}
        justifyContent="center"
        alignItems="center"
        style={{
          width: isOrder === true ? widthPercentageToDP(75) : imageLength,
          height: imageLength * aspectRatioDecimal,
        }}
      >
        <ImageBackground
          source={require("assets/icon_verifikasi.png")}
          style={[
            {
              width: iconDimension,
              height: iconDimension,
              position: "absolute",
              alignSelf: "center",
            },
          ]}
        />
        {value && (
          <Image
            source={{ uri: value.uri }}
            style={{
              width:
                isOrder === true
                  ? widthPercentageToDP(75)
                  : imageLength - responsive(10),
              height: imageLength * aspectRatioDecimal - responsive(10),
            }}
          />
        )}
        {!value && (
          <Text textAlign="center" pt={100}>
            {text}
          </Text>
        )}
      </Div>
      <Div flexDir="row" justifyContent="space-around" my="10">
        <Button
          w="44%"
          bg="primary"
          alignSelf="center"
          onPress={() => pickImage(ImagePicker.launchCameraAsync)}
        >
          <Text fontWeight="bold" color="white">
            Ambil Foto
          </Text>
        </Button>
        <Button
          w="44%"
          bg="primary"
          alignSelf="center"
          onPress={() => pickImage(ImagePicker.launchImageLibraryAsync)}
        >
          <Text fontWeight="bold" color="white">
            Galeri
          </Text>
        </Button>
      </Div>
    </Div>
  )
}
