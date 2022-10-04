import deepEqual from "deep-equal"
import React, { useContext, useEffect, useMemo, useState } from "react"

import useDebounce from "hooks/useDebounce"
import useStorageState from "hooks/useStorageState"

import useCartData from "api/hooks/cart/useCartData"
import useCartSyncMutation from "api/hooks/cart/useCartSyncMutation"

import { Cart } from "types/Cart"
import { ProductUnit } from "types/POS/ProductUnit/ProductUnit"

import { useAuth } from "./Auth"

export type CartData = {
  productUnitId: number
  quantity: number
  is_ready: boolean
  location: any
  sku: string
}

export type ExtraCartData = CartData & {
  productUnitData?: Partial<ProductUnit>
}

export enum SelectedType {
  NONE,
  PARTIAL,
  ALL,
}

type AddItemData = {
  is_ready?: boolean
  productUnitId: number
  quantity?: number
  productUnitData: ProductUnit
}

type ProviderType = {
  cartData: ExtraCartData[]
  selectedCartData: number[]
  filteredCartDataBySelected: ExtraCartData[]
  setSelectedCartData: React.Dispatch<React.SetStateAction<number[]>>
  toggleSelectedOnId: (productUnitId: number) => void
  quantity: number
  totalPrice: number
  enoughStock: boolean
  cartIsFetching: boolean
  refetchCart: () => void
  resetCart: () => void
  addItem: (itemData: AddItemData[]) => void
  overrideIfExist: (itemData: AddItemData[]) => void
  updateItemQuantity: (productUnitId: number, quantity: number) => void
  isReadyStock: (productUnitId: number, isReady: boolean) => void
  setLocation: (productUnitId: number, location: any) => void
  reduceItem: (productUnitId: number) => void
  removeItem: (productUnitId: number) => void
  clearSelectedCartItem: () => void
}

export const CartContext = React.createContext<ProviderType>({
  cartData: [],
  selectedCartData: [],
  filteredCartDataBySelected: [],
  setSelectedCartData: () => {},
  toggleSelectedOnId: () => {},
  quantity: 0,
  totalPrice: 0,
  enoughStock: true,
  cartIsFetching: false,
  refetchCart: () => {},
  resetCart: () => {},
  addItem: () => {},
  overrideIfExist: () => {},
  updateItemQuantity: () => {},
  isReadyStock: () => {},
  setLocation: () => {},
  reduceItem: () => {},
  removeItem: () => {},
  clearSelectedCartItem: () => {},
})

export const useCart = () => {
  return useContext(CartContext)
}

export const CartConsumer = CartContext.Consumer

export const CartProvider = (props) => {
  const [rawCartData, setCartData] = useState<ExtraCartData[] | null>(null)
  const [selectedCartData, setSelectedCartData] = useStorageState<number[]>(
    "selectedCartItems",
    [],
  )

  const { loggedIn } = useAuth()

  const {
    data: externalCartData,
    refetch,
    isFetching,
  } = useCartData(loggedIn, {
    onSuccess: (res: Cart) => {
      if (!res) {
        return
      }
      // We only do this on first load/login change. This fully avoid bugs if we keep asking the data from remote after syncing
      // Mostly timing issue (Sync => Sync => Get => Get. Value jumps around and wrong since sync again with wrong value)
      // Instead here we just assume the user will only access the account from 1 phone at a time.
      if (!rawCartData) {
        setCartData(
          res.items.map(
            (item) =>
              ({
                productUnitId: item.id,
                quantity: item.quantity,
                productUnitData: {
                  id: item.id,
                  name: item.name,
                  price: item.totalPrice,
                  colour: item.colour,
                  covering: item.covering,
                  sku: item.sku,
                  // TODO: Add more metadata once API is updated
                },
              } as ExtraCartData),
          ),
        )
      } else {
        // If we still get it, we simply update the meta data. This is important for stock changes
        setCartData((currentCartData) =>
          currentCartData.map((currentItem) => ({
            ...currentItem,
            ...res.items
              .map(
                (item) =>
                  ({
                    productUnitId: item.id,
                    quantity: item.quantity,
                    productUnitData: {
                      id: item.id,
                      name: item.name,
                      price: item.unitPrice,
                      colour: item.colour,
                      covering: item.covering,
                      sku: item.sku,
                    },
                  } as ExtraCartData),
              )
              ?.find(
                (newItem) =>
                  newItem.productUnitId === currentItem.productUnitData.id,
              ),
          })),
        )
      }
    },
  })

  const [syncCart] = useCartSyncMutation()

  const debouncedCartData = useDebounce(rawCartData, 2500)
  // Sync cart data to server when it changes
  // This will probably still mess up in certain scenarios
  // On first load, it might override local to remote first
  useEffect(() => {
    if (
      loggedIn &&
      externalCartData &&
      !!debouncedCartData &&
      !deepEqual(debouncedCartData, externalCartData)
    ) {
      syncCart({
        items: debouncedCartData.map((row) => ({
          quantity: row.quantity,
          id: row.productUnitId,
          sku: row.sku,
        })),
      })
    }
  }, [debouncedCartData, externalCartData, loggedIn])

  // Default to [] so we don't get any errors on processing
  const cartData = useMemo(
    () => (loggedIn ? rawCartData ?? [] : []),
    [loggedIn, rawCartData],
  )

  const filteredCartDataBySelected = useMemo(
    () => cartData.filter((x) => selectedCartData.includes(x.productUnitId)),
    [cartData, selectedCartData],
  )

  const quantity = useMemo(
    () =>
      filteredCartDataBySelected?.reduce((acc, val) => acc + val.quantity, 0) ??
      0,
    [filteredCartDataBySelected],
  )
  const totalPrice = useMemo(
    () =>
      filteredCartDataBySelected?.reduce(
        (acc, item) => acc + item.productUnitData.price * item.quantity,
        0,
      ) ?? 0,
    [filteredCartDataBySelected],
  )

  // Always accept order regardless of stock
  const enoughStock = true

  const resetCart = () => {
    setCartData([])
  }

  const addItem = (
    itemData: AddItemData[],
    quantityModifier = (previousQuantity: number, addedQuantity: number) =>
      previousQuantity + addedQuantity,
  ) => {
    let idsAdded = []

    const newCartData = itemData.reduce((prevCartData, itemToAdd) => {
      const { quantity: q, productUnitId, productUnitData } = itemToAdd
      const quantity = q ? q : 1

      const theItem = prevCartData.find(
        (item) => item.productUnitId === productUnitId,
      )

      if (!!theItem) {
        // Modify the existing item
        const restOfItem = prevCartData.filter(
          (item) => item.productUnitId !== productUnitId,
        )
        const changedItem = {
          ...theItem,
          quantity: quantityModifier(theItem.quantity, quantity),
        }

        return [...restOfItem, changedItem]
      } else {
        // Add the item to the list
        const newItem: ExtraCartData = {
          productUnitId,
          quantity,
          productUnitData,
        }

        // When we create a new item, we want it to be selected by default
        idsAdded.push(productUnitId)

        return [...prevCartData, newItem]
      }
    }, cartData)

    setCartData(newCartData)
    setSelectedCartData([...selectedCartData, ...idsAdded])
  }

  const overrideIfExist = (itemData: AddItemData[]) => {
    addItem(itemData, (previousQuantity, addedQuantity) => addedQuantity)
  }

  const updateItemQuantity = (productUnitId, quantity) => {
    const theItem = cartData.find(
      (item) => item.productUnitId === productUnitId,
    )
    const restOfItem = cartData.filter(
      (item) => item.productUnitId !== productUnitId,
    )
    if (quantity === 0) {
      // Remove item from the list
      setCartData(restOfItem)
    } else {
      const changedItem = { ...theItem, quantity }
      setCartData([...restOfItem, changedItem])
    }
  }

  const isReadyStock = (productUnitId, isReady) => {
    const theItem = cartData.find(
      (item) => item.productUnitId === productUnitId,
    )
    const restOfItem = cartData.filter(
      (item) => item.productUnitId !== productUnitId,
    )
    const changedItem = { ...theItem, is_ready: isReady }
    setCartData([...restOfItem, changedItem])
  }

  const setLocation = (productUnitId, location) => {
    const theItem = cartData.find(
      (item) => item.productUnitId === productUnitId,
    )
    const restOfItem = cartData.filter(
      (item) => item.productUnitId !== productUnitId,
    )
    const changedItem = { ...theItem, location: location }
    setCartData([...restOfItem, changedItem])
  }

  const reduceItem = (productUnitId) => {
    const theItem = cartData.find(
      (item) => item.productUnitId === productUnitId,
    )
    const restOfItem = cartData.filter(
      (item) => item.productUnitId !== productUnitId,
    )

    if (theItem.quantity === 1) {
      // Remove item from the list
      setCartData(restOfItem)
    } else {
      // Reduce by 1
      const changedItem = { ...theItem, quantity: theItem.quantity - 1 }

      setCartData([...restOfItem, changedItem])
    }
  }

  const removeItem = (productUnitId) => {
    const restOfItem = cartData.filter(
      (item) => item.productUnitId !== productUnitId,
    )
    setCartData(restOfItem)
  }

  const toggleSelectedOnId = (productUnitId: number) => {
    if (selectedCartData.includes(productUnitId)) {
      setSelectedCartData(selectedCartData.filter((x) => x !== productUnitId))
    } else {
      setSelectedCartData([...selectedCartData, productUnitId])
    }
  }

  const clearSelectedCartItem = () => {
    const cartDataWithoutSelected = cartData.filter(
      (x) => !selectedCartData.includes(x.productUnitId),
    )
    setCartData(cartDataWithoutSelected)
    setSelectedCartData([])
  }

  return (
    <CartContext.Provider
      value={{
        cartData: cartData.sort((a, b) => a.productUnitId - b.productUnitId),
        selectedCartData,
        filteredCartDataBySelected: filteredCartDataBySelected.sort(
          (a, b) => a.productUnitId - b.productUnitId,
        ),
        setSelectedCartData,
        toggleSelectedOnId,
        resetCart,
        addItem,
        overrideIfExist,
        updateItemQuantity,
        isReadyStock,
        setLocation,
        reduceItem,
        removeItem,
        clearSelectedCartItem,
        quantity,
        totalPrice,
        enoughStock,
        cartIsFetching: isFetching,
        refetchCart: refetch,
      }}
    >
      {props.children}
    </CartContext.Provider>
  )
}
