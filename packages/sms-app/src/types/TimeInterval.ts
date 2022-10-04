import { sub, startOfToday } from "date-fns"

export type TimeInterval = "today" | "week" | "month"
export const TimeInvervalList = ["today", "week", "month"]

type TimeIntervalConfigObject = {
  title: string
  value: Date
}

export const timeIntervalConfig: Record<
  TimeInterval,
  TimeIntervalConfigObject
> = {
  today: {
    title: "Today",
    value: startOfToday(),
  },
  week: {
    title: "Week",
    value: sub(new Date(), { weeks: 1 }),
  },
  month: {
    title: "Month",
    value: sub(new Date(), { months: 1 }),
  },
}
