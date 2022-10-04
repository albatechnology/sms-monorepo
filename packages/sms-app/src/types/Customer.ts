import Case from "case"

import { PersonTitle } from "api/generated/enums"
import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type Customer = {
  id: number
  title: PersonTitle
  firstName: string
  dateOfBirth: Date
  lastName: Nullable<string>
  email: string
  phone: string
  description: Nullable<string>
  defaultAddressId: Nullable<number>
}

export const mapCustomer = (
  apiObj: UnwrapOpenAPIResponse<V1Api["customerShow"]>,
): Customer => {
  return {
    id: apiObj.id,
    title: apiObj.title,
    firstName: apiObj.first_name,
    lastName: apiObj.last_name,
    dateOfBirth: apiObj.date_of_birth ? new Date(apiObj.date_of_birth) : null,
    email: apiObj.email,
    phone: apiObj.phone,
    description: apiObj.description,
    defaultAddressId: apiObj.default_address_id,
  }
}

export const getFullName = (customer: Customer) =>
  `${Case.title(customer.title)} ${customer.firstName}${
    customer.lastName ? " " + customer.lastName : ""
  }`
export const getInitials = (customer: Customer) =>
  `${customer.firstName[0]}${customer.lastName ? customer.lastName[0] : ""}`
