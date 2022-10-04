type Toast = React.RefObject<
  import("react-native-magnus").SnackbarRef
>["current"]

declare global {
  const toast: Toast["show"]
  const toastRef: Toast
}

declare var toast: Toast["show"]
declare var toastRef: Toast

type Nullable<T> = T | null
type Writeable<T> = { -readonly [P in keyof T]: T[P] }
