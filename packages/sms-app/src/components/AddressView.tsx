import React from "react"

import Text from "components/Text"

import { Address } from "types/Address"

type PropTypes = {
  address: Address
}

export default function AddressView({
  address: {
    addressLine1,
    addressLine2,
    addressLine3,
    city,
    province,
    phone,
    postcode,
  },
}: PropTypes) {
  return (
    <>
      <Text mb={5}>{addressLine1}</Text>
      {!!addressLine2 && <Text mb={5}>{addressLine2}</Text>}
      {!!addressLine3 && <Text mb={5}>{addressLine3}</Text>}
      <Text mb={5}>{city}</Text>
      <Text mb={5}>{province}</Text>
      <Text mb={10}>{postcode}</Text>
      <Text>{phone}</Text>
    </>
  )
}
