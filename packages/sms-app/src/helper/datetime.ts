import { endOfMonth, startOfMonth } from "date-fns"
import { format, zonedTimeToUtc } from "date-fns-tz"

const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone

export const formatAsUTCISO = (date: Date) => {
  return format(date, "yyyy-MM-dd HH:mm:ssXXX", { timeZone: "UTC" })
}

export const getStartOfMonthUTCFormatted = (date: Date) => {
  return formatAsUTCISO(zonedTimeToUtc(startOfMonth(date), timeZone))
}

export const getEndOfMonthUTCFormatted = (date: Date) => {
  return formatAsUTCISO(zonedTimeToUtc(endOfMonth(date), timeZone))
}
