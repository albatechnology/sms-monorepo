import { NavigationContainerRef } from "@react-navigation/native"
import Constants from "expo-constants"
import * as Notifications from "expo-notifications"
import { useState, useEffect, useRef } from "react"
import { Platform } from "react-native"

import useApi from "hooks/useApi"

import { useAuth } from "providers/Auth"

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
})

export function PushNotificationHandler({
  reset,
}: {
  reset: NavigationContainerRef["reset"]
}) {
  const [expoPushToken, setExpoPushToken] = useState<string | null>(null)
  const [navigationQueued, setNavigationQueued] = useState<any>(null)
  // const [notification, setNotification] = useState<
  //   Notifications.Notification | boolean
  // >(false)
  // const notificationListener = useRef<
  //   ReturnType<typeof Notifications.addNotificationReceivedListener>
  // >()

  const { loggedIn } = useAuth()
  const api = useApi()
  useEffect(() => {
    if (expoPushToken !== null) {
      if (loggedIn) {
        api.pushNotificationSubscribe({ code: expoPushToken }).then(() => {
          console.log("Subscribed to push notification")
        })
      } else {
        api.pushNotificationUnsubscribe({ code: expoPushToken })
      }
    }
  }, [expoPushToken, loggedIn, api])

  const responseListener =
    useRef<
      ReturnType<typeof Notifications.addNotificationResponseReceivedListener>
    >()

  useEffect(() => {
    if (!!reset && !!navigationQueued) {
      reset(navigationQueued)
      setNavigationQueued(null)
    }
  }, [reset, navigationQueued])

  useEffect(() => {
    registerForPushNotificationsAsync()
      .then((token) => setExpoPushToken(token))
      .catch(console.error)

    // This listener is fired whenever a notification is received while the app is foregrounded
    // notificationListener.current = Notifications.addNotificationReceivedListener(
    //   (notification) => {
    //     TODO: Invalidate cache
    //     setNotification(notification)
    //   },
    // )

    // This listener is fired whenever a user taps on or interacts with a notification (works when app is foregrounded, backgrounded, or killed)
    responseListener.current =
      Notifications.addNotificationResponseReceivedListener((response) => {
        const path = response.notification.request.content.data.link as string

        const parsedPath = JSON.parse(path.replace(/'/g, '"'))

        setNavigationQueued(parsedPath)
      })

    return () => {
      // Notifications.removeNotificationSubscription(notificationListener.current)
      Notifications.removeNotificationSubscription(responseListener.current)
    }
  }, [])

  return null
}

export async function registerForPushNotificationsAsync() {
  let token
  if (Constants.isDevice) {
    const { status: existingStatus } = await Notifications.getPermissionsAsync()
    let finalStatus = existingStatus
    if (existingStatus !== "granted") {
      const { status } = await Notifications.requestPermissionsAsync()
      finalStatus = status
    }
    if (finalStatus !== "granted") {
      alert("Failed to get push token for push notification!")
      throw new Error("Failed to get push token for push notification!")
    }
    token = (await Notifications.getExpoPushTokenAsync()).data
  } else {
    alert("Must use physical device for Push Notifications")
  }

  if (Platform.OS === "android") {
    Notifications.setNotificationChannelAsync("default", {
      name: "default",
      importance: Notifications.AndroidImportance.MAX,
      vibrationPattern: [0, 250, 250, 250],
      lightColor: "#FF231F7C",
    })
  }

  return token
}
