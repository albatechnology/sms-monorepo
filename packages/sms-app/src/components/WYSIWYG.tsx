import iframe from "@native-html/iframe-plugin"
import React from "react"
import { Dimensions } from "react-native"
import HTML from "react-native-render-html"
import WebView from "react-native-webview"

import { responsive } from "helper"

type PropTypes = {
  body: string
  horizontalPadding?: number
}

export default ({ body, horizontalPadding = 0 }: PropTypes) => {
  if (!body) {
    return null
  }

  const renderers = {
    iframe,
  }

  const finalHtml = body
    .replace(/font-family: (.+?);/g, "")
    .replace(/font-size: (.+?);/g, "")
    .replace(/width="(.+?)"/g, "")
    .replace(/height="(.+?)"/g, "")

  return (
    <HTML
      renderers={renderers}
      WebView={WebView}
      source={{
        html: finalHtml,
      }}
      baseStyle={{
        fontSize: responsive(12),
        color: "#222B45",
        lineHeight: 20,
      }}
      tagsStyles={{
        strong: {
          fontWeight: "normal",
          fontFamily: "FontBold",
        },
      }}
      contentWidth={Dimensions.get("window").width - horizontalPadding * 2}
      renderersProps={{ iframe: { scalesPageToFit: true } }}
    />
  )
}
