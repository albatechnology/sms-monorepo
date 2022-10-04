import React, { useEffect, useState } from "react"
import {
  ImageSourcePropType,
  StyleProp,
  Image,
  ImageStyle,
  ImageURISource,
  Platform,
} from "react-native"
import { Image as CachedImage } from "react-native-expo-image-cache"

type PropTypes = {
  style?: StyleProp<ImageStyle>
  source: ImageSourcePropType
  scalable?: boolean
  height?: number
  width?: number
  [key: string]: any
}

const calculateScaling = (
  width: number,
  height: number,
  aspectRatio: number,
) => {
  //Width and height provided, then ignore scalable
  if (width && !height) {
    return { width, height: (1 / aspectRatio) * width }
  }
  if (!width && height) {
    return { width: aspectRatio * height, height }
  }
}

export default ({
  style,
  source,
  scalable = false,
  width,
  height,
  ...rest
}: PropTypes) => {
  const [aspectRatio, setAspectRatio] = useState<number | null>(1)

  useEffect(() => {
    if (Platform.OS === "web") {
      setAspectRatio(1)
    } else if (scalable) {
      const imageSource = source as ImageURISource
      // Online image src
      if (!!imageSource?.uri || imageSource?.uri === null) {
        Image.getSize(
          imageSource.uri ?? "https://via.placeholder.com/404",
          (width, height) => {
            setAspectRatio(width / height)
          },
          () => {},
        )
      }
      //Local
      else {
        const { width, height } = Image.resolveAssetSource(source)
        setAspectRatio(width / height)
      }
    }
  }, [width, height, source, scalable, setAspectRatio])

  const scaling = scalable ? calculateScaling(width, height, aspectRatio) : {}

  if (
    ((source as ImageURISource)?.uri ||
      (source as ImageURISource)?.uri === null) &&
    Platform.OS !== "web"
  ) {
    return (
      <CachedImage
        style={[style, scaling]}
        uri={
          (source as ImageURISource)?.uri ?? "https://via.placeholder.com/404"
        }
        {...rest}
      />
    )
  }

  return <Image style={[style, scaling]} source={source} {...rest} />
}
