import { V1Api } from "api/openapi"

import { loadLocalImageToBase64 } from "helper/pictures"

import { Channel } from "./Channel"
import { CompanyAccount, mapCompanyAccount } from "./CompanyAccount"
import { ImageType, mapImage } from "./Image"
import { UnwrapOpenAPIResponse } from "./helper"

export type Company = {
  id: number
  name: string
  companyAccount: CompanyAccount
  logo: ImageType
}

export const mapCompany = (
  apiObj: UnwrapOpenAPIResponse<V1Api["companyShow"]>,
): Company => {
  return {
    id: apiObj.id,
    name: apiObj.name,
    companyAccount: apiObj.company_account
      ? mapCompanyAccount(apiObj.company_account)
      : null,
    logo: apiObj.logo ? mapImage(apiObj.logo) : null,
  }
}

export const getLogo = async (company: Company, channel: Channel) => {
  const logoImageData = await loadLocalImageToBase64(
    require("assets/quotation-logo.png"),
  )

  // Hardcode: If Channel is plaza indonesia, (id: 3), show different logo
  if (channel.id === 3) {
    const EICHHOLTZLogo = await loadLocalImageToBase64(
      require("assets/EICHHOLTZ_logo.png"),
    )
    return EICHHOLTZLogo
  }

  return company?.logo?.url ?? logoImageData
}
