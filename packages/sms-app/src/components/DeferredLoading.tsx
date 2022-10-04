import React, { useState } from "react"
import { useEffect } from "react"

import Loading from "./Loading"

type Props = { durationMs?: number }

export default function DeferredLoading({ durationMs = 2500 }: Props) {
  const [showIndicator, setShowIndicator] = useState(false)

  useEffect(() => {
    const delay = setTimeout(() => {
      setShowIndicator(true)
    }, durationMs)

    return () => {
      clearTimeout(delay)
    }
  }, [durationMs])

  if (!showIndicator) {
    return null
  }

  return <Loading />
}
