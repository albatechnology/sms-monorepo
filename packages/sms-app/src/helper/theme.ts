import { StyleSheet } from "react-native"
import { ThemeType } from "react-native-magnus"
import { RFValue } from "react-native-responsive-fontsize"

export const COLOR_PRIMARY = "#313132"
export const COLOR_DISABLED = "#C4C4C4"
export const COLOR_PLACEHOLDER = "#CFCFD380"
export const COLOR_RED = "#FC5C5D"
export const COLOR_GREEN = "#38DA8A"
export const COLOR_BLUE = "#3893CE"
export const COLOR_YELLOW = "#FFD13D"

// TO BE REMOVED LATER
export const COLOR_YELLOW_BG = "#FFD13D"
export const COLOR_YELLOW_TEXT = "#A08939"
export const COLOR_RED_BG = "#F6C6CD"
export const COLOR_RED_TEXT = "#A1504C"
export const COLOR_BLUE_BG = "#CBE4FE"
export const COLOR_BLUE_TEXT = "#1F3B62"
export const COLOR_GREY_BG = "#DADADA"
export const COLOR_GREY_TEXT = "#C4C4C4"
// END TO BE REMOVED LATER

export const COLOR_STATUS_GREEN_BG = "#38DA8A"
export const COLOR_STATUS_GREEN_TEXT = "#1D8D59"
export const COLOR_STATUS_YELLOW_BG = "#FFD13D"
export const COLOR_STATUS_YELLOW_TEXT = "#A08939"
export const COLOR_STATUS_RED_BG = "#E80D2C"
export const COLOR_STATUS_RED_TEXT = "#500400"
export const COLOR_STATUS_CLOSED_BG = "#4B4B4B"
export const COLOR_STATUS_CLOSED_TEXT = "#959595"

export const COLOR_ACTIVITY_COLD_BG = "#CBE4FE"
export const COLOR_ACTIVITY_COLD_TEXT = "#1F3B62"
export const COLOR_ACTIVITY_WARM_BG = "#FFE283"
export const COLOR_ACTIVITY_WARM_TEXT = "#A08939"
export const COLOR_ACTIVITY_HOT_BG = "#F6C6CD"
export const COLOR_ACTIVITY_HOT_TEXT = "#A1504C"
export const COLOR_ACTIVITY_CLOSED_BG = "#C4C4C4"
export const COLOR_ACTIVITY_CLOSED_TEXT = "#000000"

export const COLOR_ORDER_STATUS_QUOTATION = "#38DA8A"
export const COLOR_ORDER_STATUS_SHIPMENT = "#036BB6"
export const COLOR_ORDER_STATUS_CANCELLED = "#C4C4C4"
export const COLOR_ORDER_STATUS_RETURNED = "#FF0000"

export const COLOR_ORDER_PAYMENT_STATUS_NONE = "#C4C4C4"
export const COLOR_ORDER_PAYMENT_STATUS_PARTIAL = "#FFD13D"
export const COLOR_ORDER_PAYMENT_STATUS_DOWN_PAYMENT = "#38DA8A"
export const COLOR_ORDER_PAYMENT_STATUS_SETTLEMENT = "#036BB6"
export const COLOR_ORDER_PAYMENT_STATUS_OVER_PAYMENT = "#FF0000"
export const COLOR_ORDER_PAYMENT_STATUS_OVER_REFUNDED = "#C4C4C4"

export const theme: ThemeType = {
  fontFamily: {
    normal: "FontRegular",
    bold: "FontBold",
  },
  fontSize: {
    8: RFValue(8),
    10: RFValue(10),
    11: RFValue(11),
    12: RFValue(12),
    14: RFValue(14),
    16: RFValue(16),
    18: RFValue(18),
    20: RFValue(20),
  },
  spacing: {
    none: 0,
    0: RFValue(0),
    5: RFValue(5),
    10: RFValue(10),
    12: RFValue(12),
    13: RFValue(13),
    20: RFValue(20),
    30: RFValue(30),
    40: RFValue(40),
    "-5": RFValue(-5),
    "-10": RFValue(-10),
    "-20": RFValue(-20),
    "-30": RFValue(-30),
  },
  colors: {
    primary: COLOR_PRIMARY,
    disabled: COLOR_DISABLED,
    grey: COLOR_DISABLED,
  },
}

export default StyleSheet.create({
  //---BACKGROUND---
  bgPrimary: {
    backgroundColor: COLOR_PRIMARY,
  },
  bgWhite: {
    backgroundColor: "#FFFFFF",
  },

  //---MARGIN---
  //All
  m0: {
    margin: 0,
  },
  m5: {
    margin: RFValue(5),
  },
  m10: {
    margin: RFValue(10),
  },
  m20: {
    margin: RFValue(20),
  },
  m30: {
    margin: RFValue(30),
  },
  //Top
  mT0: {
    marginTop: 0,
  },
  mT3: {
    marginTop: RFValue(3),
  },
  mT5: {
    marginTop: RFValue(5),
  },
  mT10: {
    marginTop: RFValue(10),
  },
  mT17: {
    marginTop: RFValue(17),
  },
  mT20: {
    marginTop: RFValue(20),
  },
  mT30: {
    marginTop: RFValue(30),
  },
  mT75: {
    marginTop: RFValue(75),
  },
  //Right
  mR0: {
    marginRight: 0,
  },
  mR5: {
    marginRight: RFValue(5),
  },
  mR10: {
    marginRight: RFValue(10),
  },
  mR20: {
    marginRight: RFValue(20),
  },
  mR30: {
    marginRight: RFValue(30),
  },
  //Bottom
  mB0: {
    marginBottom: 0,
  },
  mB5: {
    marginBottom: RFValue(5),
  },
  mB10: {
    marginBottom: RFValue(10),
  },
  mB20: {
    marginBottom: RFValue(20),
  },
  mB30: {
    marginBottom: RFValue(30),
  },
  //Left
  mL0: {
    marginLeft: 0,
  },
  mL5: {
    marginLeft: RFValue(5),
  },
  mL10: {
    marginLeft: RFValue(10),
  },
  mL16: {
    marginLeft: RFValue(16),
  },
  mL20: {
    marginLeft: RFValue(20),
  },
  mL30: {
    marginLeft: RFValue(30),
  },
  //Horizontal
  mX0: {
    marginHorizontal: 0,
  },
  mX5: {
    marginHorizontal: RFValue(5),
  },
  mX10: {
    marginHorizontal: RFValue(10),
  },
  mX14: {
    marginHorizontal: RFValue(14),
  },
  mX16: {
    marginHorizontal: RFValue(16),
  },
  mX20: {
    marginHorizontal: RFValue(20),
  },
  mX30: {
    marginHorizontal: RFValue(30),
  },
  mX40: {
    marginHorizontal: RFValue(40),
  },
  //Vertical
  mY0: {
    marginVertical: 0,
  },
  mY5: {
    marginVertical: RFValue(5),
  },
  mY10: {
    marginVertical: RFValue(10),
  },
  mY16: {
    marginVertical: RFValue(16),
  },
  mY20: {
    marginVertical: RFValue(20),
  },
  mY30: {
    marginVertical: RFValue(30),
  },

  //---PADDING---
  //All
  p0: {
    padding: 0,
  },
  p5: {
    padding: RFValue(5),
  },
  p10: {
    padding: RFValue(10),
  },
  p20: {
    padding: RFValue(20),
  },
  p30: {
    padding: RFValue(30),
  },
  //Top
  pT0: {
    paddingTop: 0,
  },
  pT5: {
    paddingTop: RFValue(5),
  },
  pT10: {
    paddingTop: RFValue(10),
  },
  pT20: {
    paddingTop: RFValue(20),
  },
  pT24: {
    paddingTop: RFValue(24),
  },
  pT30: {
    paddingTop: RFValue(30),
  },
  pT100: {
    paddingTop: RFValue(100),
  },
  //Right
  pR0: {
    paddingRight: 0,
  },
  pR5: {
    paddingRight: RFValue(5),
  },
  pR10: {
    paddingRight: RFValue(10),
  },
  pR16: {
    paddingRight: RFValue(16),
  },
  pR20: {
    paddingRight: RFValue(20),
  },
  pR30: {
    paddingRight: RFValue(30),
  },
  //Bottom
  pB0: {
    paddingBottom: 0,
  },
  pB5: {
    paddingBottom: RFValue(5),
  },
  pB10: {
    paddingBottom: RFValue(10),
  },
  pB20: {
    paddingBottom: RFValue(20),
  },
  pB30: {
    paddingBottom: RFValue(30),
  },
  //Left
  pL0: {
    paddingLeft: 0,
  },
  pL5: {
    paddingLeft: RFValue(5),
  },
  pL10: {
    paddingLeft: RFValue(10),
  },
  pL16: {
    paddingLeft: RFValue(16),
  },
  pmL20: {
    paddingLeft: RFValue(20),
  },
  pL30: {
    paddingLeft: RFValue(30),
  },
  //Horizontal
  pX0: {
    paddingHorizontal: 0,
  },
  pX4: {
    paddingHorizontal: RFValue(4),
  },
  pX5: {
    paddingHorizontal: RFValue(5),
  },
  pX10: {
    paddingHorizontal: RFValue(10),
  },
  pX16: {
    paddingHorizontal: RFValue(16),
  },
  pX20: {
    paddingHorizontal: RFValue(20),
  },
  pX30: {
    paddingHorizontal: RFValue(30),
  },
  pX40: {
    paddingHorizontal: RFValue(40),
  },
  //Vertical
  pY0: {
    paddingVertical: 0,
  },
  pY5: {
    paddingVertical: RFValue(5),
  },
  pY10: {
    paddingVertical: RFValue(10),
  },
  pY15: {
    paddingVertical: RFValue(15),
  },
  pY20: {
    paddingVertical: RFValue(20),
  },
  pY30: {
    paddingVertical: RFValue(30),
  },
})
