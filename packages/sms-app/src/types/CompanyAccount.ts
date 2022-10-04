import { V1Api } from "api/openapi"

import { UnwrapOpenAPIResponse } from "./helper"

export type CompanyAccount = {
  id: number
  name: string
  bankName: string
  accountName: string
  accountNumber: string
}

export const mapCompanyAccount = (
  apiObj: UnwrapOpenAPIResponse<V1Api["companyAccountIndex"]>[number],
): CompanyAccount => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    bankName: apiObj.bank_name,
    accountName: apiObj.account_name,
    accountNumber: apiObj.account_number,
  }
}
