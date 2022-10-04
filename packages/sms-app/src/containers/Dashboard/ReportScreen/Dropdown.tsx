import React, { FC, ReactElement, useRef, useState } from "react"
import {
  FlatList,
  StyleSheet,
  Text,
  TouchableOpacity,
  Modal,
  View,
} from "react-native"
import { Div, Icon, Input } from "react-native-magnus"
import {
  heightPercentageToDP,
  widthPercentageToDP,
} from "react-native-responsive-screen"

interface Props {
  label: string
  data: Array<{ label: string; value: string }>
  onSelect: (item: { label: string; value: string }) => void
}

const Dropdown: FC<Props> = ({ label, data, onSelect }) => {
  const DropdownButton = useRef()
  const [visible, setVisible] = useState(false)
  const [selected, setSelected] = useState(undefined)
  const [dropdownTop, setDropdownTop] = useState(0)

  const toggleDropdown = (): void => {
    visible ? setVisible(false) : openDropdown()
  }

  const openDropdown = (): void => {
    DropdownButton.current.measure(
      (
        _fx: number,
        _fy: number,
        _w: number,
        h: number,
        _px: number,
        py: number,
      ) => {
        setDropdownTop(py + h)
      },
    )
    setVisible(true)
  }

  const onItemPress = (item: any): void => {
    setSelected(item)
    onSelect(item)
    setVisible(false)
  }

  const renderItem = ({ item }: any): ReactElement<any, any> => (
    <TouchableOpacity style={styles.item} onPress={() => onItemPress(item)}>
      <Text>{item.label}</Text>
    </TouchableOpacity>
  )

  const renderDropdown = (): ReactElement<any, any> => {
    return (
      <Modal visible={visible} transparent animationType="none">
        <TouchableOpacity
          style={styles.overlay}
          onPress={() => setVisible(false)}
        >
          <View style={[styles.dropdown, { top: dropdownTop }]}>
            <FlatList
              data={data}
              renderItem={renderItem}
              keyExtractor={(item, index) => index.toString()}
            />
          </View>
        </TouchableOpacity>
      </Modal>
    )
  }

  return (
    <Div row>
      <TouchableOpacity
        ref={DropdownButton}
        style={styles.button}
        onPress={toggleDropdown}
      >
        {renderDropdown()}
        <Text style={styles.buttonText}>
          {(!!selected && selected.label) || label}
        </Text>
        <Icon
          style={styles.icon}
          fontFamily="FontAwesome"
          name="chevron-down"
        />
      </TouchableOpacity>
      <Input
        placeholder="Search.."
        // p={10}
        w={widthPercentageToDP(65)}
        ml={heightPercentageToDP(1)}
        mt={heightPercentageToDP(2)}
        mb={heightPercentageToDP(1)}
        h={heightPercentageToDP(6.5)}
        focusBorderColor="blue700"
        prefix={<Icon name="search" color="gray900" fontFamily="Feather" />}
      />
    </Div>
  )
}

const styles = StyleSheet.create({
  button: {
    borderRadius: 10,
    flexDirection: "row",
    alignItems: "center",
    marginTop: 15,
    marginLeft: 10,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: "#c4c4c4",
    backgroundColor: "#fff",
    height: 50,
    zIndex: 1,
    width: "25%",
  },
  buttonText: {
    flex: 1,
    textAlign: "center",
  },
  icon: {
    marginRight: 10,
  },
  dropdown: {
    position: "absolute",
    // justifyContent:'flex-end',
    backgroundColor: "#fff",
    width: "50%",
    marginLeft: heightPercentageToDP(1),
    shadowColor: "#000000",
    shadowRadius: 4,
    shadowOffset: { height: 4, width: 0 },
    shadowOpacity: 0.5,
  },
  overlay: {
    width: "50%",
    height: "100%",
  },
  item: {
    paddingHorizontal: 10,
    paddingVertical: 10,
    borderBottomWidth: 1,
  },
})

export default Dropdown
