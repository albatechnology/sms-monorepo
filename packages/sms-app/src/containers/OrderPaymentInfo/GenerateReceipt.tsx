import Case from "case"
import * as Print from "expo-print"
import * as Sharing from "expo-sharing"

import { formatCurrency, formatDate } from "helper"

import { Company, getLogo } from "types/Company"
import { getFullName } from "types/Customer"
import { Order } from "types/Order"
import { Payment } from "types/Payment/Payment"

const htmlContent = async (
  order: Order,
  payment: Payment,
  company: Company,
) => {
  const logo = await getLogo(company, order.channel)

  return `<!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Pdf Content</title>
      <style>
        @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap");
        @media print {
          footer {
            break-before: page;
          }
        }
        html {
          background-color: white;
        }
        body {
          font-size: 14px;
          font-family: Open Sans, Arial, Helvetica, sans-serif;
          min-height: 100%;
          margin: 2,5%;
        }
        footer {
          font-size: 14px;
          font-family: Open Sans, Arial, Helvetica, sans-serif;
        }
        h1 {
          text-align: center;
        }
        hr {
          height: 2px;
          border-width: 0;
          color: #313132;
          background-color: #313132;
          margin-top: 24px;
        }
        .topContainer {
          display: flex;
          flex-direction: row;
          justify-content: space-between;
          margin-top: 12px;
          margin-bottom: 24px;
        }
        .logo {
          width: 20%;
          height: auto
        }
        .quotationTitle {
          font-size: 32px;
          font-weight: bold;
          text-align: center;
        }
        .metadata {
          display: flex;
          flex-direction: row;
          margin-top: 4px;
        }
        .metadata-title {
          width: 120px;
          font-weight: bold;
          flex-shrink: 0;
        }
      </style>
    </head>
    <body>
      <div class="topContainer">
        <img
          src="${logo}"
          class="logo"
          alt="Logo"
        />
        <div>
          <div class="quotationTitle">Receipt of Payment</div>
          <div class="metadata">
            <div class="metadata-title">Customer Name</div>
            <div>: ${getFullName(order.customer)}</div>
          </div>
          <div class="metadata">
            <div class="metadata-title">Invoice No.</div>
            <div>: ${order.invoiceNumber}</div>
          </div>
          <div class="metadata">
            <div class="metadata-title">Date</div>
            <div>: ${formatDate(payment.createdAt)}</div>
          </div>
        </div>
      </div>

      <hr />

      <div>
        <div class="metadata">
          <div class="metadata-title">Order Total</div>
          <div>: ${formatCurrency(order.totalPrice)}</div>
        </div>
        <div class="metadata">
          <div class="metadata-title">Payment ID</div>
          <div>: ${payment.id}</div>
        </div>
        <div class="metadata">
          <div class="metadata-title">Payment Amount</div>
          <div>: ${formatCurrency(payment.amount)}</div>
        </div>
        <div class="metadata">
          <div class="metadata-title">Payment Type</div>
          <div>: ${payment.paymentType.name}</div>
        </div>
      </div>

      <div
        style="
          color: white;
          background-color: #313132;
          font-size: 24px;
          font-weight: bold;
          text-align: center;
          padding-top: 12px;
          padding-bottom: 12px;
          margin-top: 12px;
        "
      >
        THANK YOU FOR YOUR ORDER AT <br/> ${
          company?.companyAccount?.name
            ? Case.upper(company.companyAccount.name)
            : "OUR COMPANY"
        }
      </div>
    </body>
  </html>
  `
}

const GenerateReceiptPdf = async (
  order: Order,
  payment: Payment,
  company: Company,
) => {
  try {
    const { uri } = await Print.printToFileAsync({
      html: await htmlContent(order, payment, company),
      width: 793,
      height: 561,
    })
    Sharing.shareAsync(uri)
  } catch (err) {
    toast("Maaf, gagal export. Mohon coba lagi.")
    console.error(err)
  }
}

export default GenerateReceiptPdf
