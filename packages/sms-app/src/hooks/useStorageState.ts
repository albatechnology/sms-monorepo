import { Dispatch, useEffect, useState, useLayoutEffect } from "react"

import storage from "../storage"

const getValue = async (key: string) => {
  try {
    return await storage.load({ key, id: "1" })
  } catch (e) {
    // console.warn(e)
  }
}

const saveValue = async (key: string, data: any) => {
  return await storage.save({
    key,
    data,
    // We set the id here so that we can clear the storage using storage.clearMap
    id: "1",
  })
}

export default function useAsyncStorage<T>(
  key: string,
  initialValue: T,
): [T, Dispatch<React.SetStateAction<T>>] {
  const [value, setValue] = useState(initialValue)

  useLayoutEffect(() => {
    ;(async () => {
      try {
        const retrievedValue = await getValue(key)
        if (retrievedValue !== null && retrievedValue !== undefined) {
          setValue(retrievedValue)
        }
      } catch (e) {
        // console.error(e)
      }
    })()
  }, [])

  useEffect(() => {
    saveValue(key, value)
  }, [value])

  return [value, setValue]
}
