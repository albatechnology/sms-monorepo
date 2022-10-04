import React, { useCallback, useState } from "react"
import { TouchableWithoutFeedback } from "react-native"
import { Icon } from "react-native-magnus"

type UseToggleableSecureEntryReturn = {
  secureTextEntry: boolean
  toggleSecureEntry: () => void
  eyeIcon: (props?: any) => JSX.Element
}

export default (): UseToggleableSecureEntryReturn => {
  const [secureTextEntry, setSecureTextEntry] = useState(true)

  const toggleSecureEntry = () => {
    setSecureTextEntry((prev) => !prev)
  }

  const eyeIcon = useCallback(
    (props) => (
      <TouchableWithoutFeedback onPress={toggleSecureEntry}>
        <Icon
          {...props}
          name={secureTextEntry ? "eye-off" : "eye"}
          fontFamily="Ionicons"
          color="grey"
          fontSize={20}
        />
      </TouchableWithoutFeedback>
    ),
    [toggleSecureEntry, secureTextEntry],
  )

  return { secureTextEntry, toggleSecureEntry, eyeIcon }
}
