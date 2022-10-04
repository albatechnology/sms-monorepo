import React, { useContext, useEffect, useState } from "react"

import useStorageState from "hooks/useStorageState"

import { queryClient } from "../query"

export type AuthType = {
  jwt?: string
}

const initialData: AuthType = {
  jwt: undefined,
}

type ProviderType = {
  loggedIn: boolean
  data: AuthType
  onLogin: (jwt: string) => void
  onLogout: () => void
  isLoading: boolean
}

export const AuthContext = React.createContext<ProviderType>({
  loggedIn: true,
  data: { jwt: "" },
  onLogin: () => {},
  onLogout: () => {},
  isLoading: true,
})

export const useAuth = () => {
  return useContext(AuthContext)
}

export const AuthConsumer = AuthContext.Consumer

export const AuthProvider = (props) => {
  const [data, setData] = useStorageState<AuthType>("auth", initialData)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    if (!!data && data.jwt !== undefined) {
      setIsLoading(false)
    }
  }, [data, setIsLoading])

  const onLogin = (newJwt) => {
    setData((prevData) => ({ ...prevData, jwt: newJwt }))
  }

  const onLogout = () => {
    setData((prevData) => ({ ...prevData, jwt: null }))
    // Clear all API cache. Makes sure we have correct data
    queryClient.clear()
  }

  const loggedIn = !!data.jwt && data.jwt !== ""

  return (
    <AuthContext.Provider
      value={{ loggedIn, data, onLogin, onLogout, isLoading }}
    >
      {props.children}
    </AuthContext.Provider>
  )
}
