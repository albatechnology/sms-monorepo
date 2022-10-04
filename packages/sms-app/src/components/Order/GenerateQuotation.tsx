import Case from "case"
import * as Print from "expo-print"
import * as Sharing from "expo-sharing"
import { useEffect } from "react"

import { useAxios } from "hooks/useApi"

import { useAuth } from "providers/Auth"

import { formatCurrency, formatDate } from "helper"

import { Company, getLogo } from "types/Company"
import { getFullName } from "types/Customer"
import { Order, orderPaymentStatusConfig } from "types/Order"
import { Payment } from "types/Payment/Payment"
import { User } from "types/User"

const htmlContent = async (
  order: Order,
  user: User,
  company: Company,
  paymentList: Payment[],
  isDeals: boolean,
) => {
  const logo = await getLogo(company, order.channel)
  const renderOrderList = () => {
    let res = ""
    order?.orderDetails?.forEach((item) => {
      res =
        res +
        `
        <tr>
        <td>
          <img
          src=${
            item?.photo?.length > 0
              ? item?.photo[0].url
              : item?.images?.length > 0
              ? item?.images[0]?.url
              : "https://via.placeholder.com/404"
          }
          alt="productImage"
          style="width: 126px; height: auto; display: block; margin-left: auto; margin-right: auto;" 
          />
        </td>
        <td style="text-align: left;">${item?.productUnit?.name}</td>
        <td>${item?.quantity}</td>
      
        <td>${formatCurrency(item?.unitPrice).replace(" ", "&nbsp;")}</td>
        <td>
            ${formatCurrency(item?.totalDiscount).replace(" ", "&nbsp;")}
        </td>
        <td>
            ${formatCurrency(item?.totalPrice).replace(" ", "&nbsp;")}
        </td>
        </tr>
        `
    })

    res =
      res +
      `
      <tr>
        <td colspan="5" class="info-row-left">Packing Fee</td>
        <td class="info-row-right">
          ${formatCurrency(order?.packingFee).replace(" ", "&nbsp;")}
        </td>
      </tr>
      <tr>
        <td colspan="5" class="info-row-left">Shipping Fee</td>
        <td class="info-row-right">
          ${formatCurrency(order?.shippingFee).replace(" ", "&nbsp;")}
        </td>
      </tr>
      <tr>
        <td colspan="5" class="info-row-left">Sub Total</td>
        <td class="info-row-right">
          ${formatCurrency(
            order?.originalPrice + order?.packingFee + order?.shippingFee,
          ).replace(" ", "&nbsp;")}
        </td>
      </tr>
      <tr>
        <td colspan="5" class="info-row-left">Additional Discount</td>
        <td class="info-row-right">
          ${formatCurrency(order?.additionalDiscount).replace(" ", "&nbsp;")}
        </td>
      </tr>
      <tr>
        <td colspan="5" class="info-row-left">Discount</td>
        <td class="info-row-right">
          ${formatCurrency(order?.totalDiscount).replace(" ", "&nbsp;")}
        </td>
      </tr>
      <tr>
        <td colspan="5" class="info-row-left">VAT</td>
        <td class="info-row-right">
          ${formatCurrency(0).replace(" ", "&nbsp;")}
        </td>
      </tr>
      <tr style="margin-bottom: 10px;">
        <td colspan="5" class="info-row-left">Total</td>
        <td class="info-row-right">
          ${formatCurrency(order?.totalPrice).replace(" ", "&nbsp;")}
        </td>
      </tr>
      ${paymentList
        .filter((payment) => payment.status !== "REJECTED")
        .map(
          (payment) => `
        <tr style="margin-bottom: 10px;">
          <td colspan="5" class="info-row-left" style="margin-top: 15px">${Case.title(
            payment.paymentType.name,
          )}</td>
          <td class="info-row-right">
            ${formatCurrency(payment.amount).replace(" ", "&nbsp;")}
          </td>
        </tr>
      `,
        )}
        <tr>
      ${
        isDeals
          ? `
        <td colspan="5" class="info-row-left">Payment Status</td>
          <td class="info-row-right">
            ${
              orderPaymentStatusConfig[order.paymentStatusForInvoice]
                .displayText
            }
          </td>
        </tr>`
          : ""
      }
      <tr style="border-top: 1px solid #313132;">
        <td colspan="5" class="info-row-left">&nbsp;</td>
        <td class="info-row-right">&nbsp;</td>
      </tr>
    `

    return res
  }

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
          tr {
            page-break-inside: avoid !important;
            -webkit-column-break-inside: avoid;
            break-inside: avoid;
            -webkit-region-break-inside: avoid;
          }
        }
        tr {
          page-break-inside: avoid !important;
          page-break-after: auto !important;
        }
        html {
          background-color: white;
        }
        body {
          font-size: 14px;
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
        .italic {
          font-style: italic;
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
        .table {
          width: 100%;
          margin-top: 32px;
          border-spacing: 0;
        }
        th {
          padding: 8px;
          border-top: 1px solid #313132;
          border-bottom: 1px solid #313132;
        }
        td {
          padding: 8px;
          text-align: center;
          vertical-align: middle;
        }
  
        table tr:first-child th:first-child {
          border-left: 1px solid #313132;
        }
        table tr:first-child th:last-child {
          border-right: 1px solid #313132;
        }
        .info-row-left {
          border-left: 1px solid #313132;
          text-align: right;
        }
        .info-row-right {
          border-right: 1px solid #313132;
          text-align: right;
        }
  
        table tr td:first-child {
          border-left: 1px solid #313132;
        }
        table tr td:last-child {
          border-right: 1px solid #313132;
        }
        table tr td:nth-child(n + 4) {
          text-align: right;
        }
  
        table tr:nth-last-child(5) td {
          border-top: 1px solid #313132;
        }
        table tr:last-child td {
          border-bottom: 1px solid #313132;
        }
        .alt-table {
          width: 100%;
          margin-top: 32px;
          border-spacing: 0;
        }
        .alt-table td {
          background-color: #ef633f;
          color: white;
          padding: 8px;
          border-top: 1px solid #313132;
          border-bottom: 1px solid #313132;
        }
        .alt-table tr:first-child td:first-child {
          border-left: 1px solid #313132;
          border-top-left-radius: 8px;
          border-bottom-left-radius: 8px;
        }
  
        .alt-table tr:first-child td:last-child {
          border-right: 1px solid #313132;
          border-top-right-radius: 8px;
          border-bottom-right-radius: 8px;
        }
  
        .footer-section {
          display: flex;
          flex: 1;
          flex-direction: row;
          justify-content: space-between;
          margin-top: 50px;
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
          <div class="quotationTitle">${isDeals ? "Invoice" : "Quotation"}</div>
          <div class="metadata">
            <div class="metadata-title">Date</div>
            <div>: ${formatDate(order.createdAt)}</div>
          </div>
          <div class="metadata">
            <div class="metadata-title">No.</div>
            <div>: ${order.invoiceNumber}</div>
          </div>
          <div class="metadata">
            <div class="metadata-title">Associate</div>
            <div>: ${user.name}</div>
          </div>
          ${
            !isDeals
              ? `<div class="metadata">
              <div class="metadata-title">Valid For</div>
              <div>: 14 Days</div>
            </div>`
              : ""
          }
        </div>
      </div>

      <div>
      <div class="metadata-title">Shipping To:</div>
      <div>${getFullName(order?.customer)}</div>
      <div>${order?.shippingAddress?.addressLine1}</div>
      ${
        order?.shippingAddress?.addressLine2
          ? `
      <div>${order?.shippingAddress?.addressLine2}</div>
      `
          : ""
      } ${
    order?.shippingAddress?.addressLine3
      ? `
      <div>${order?.shippingAddress?.addressLine3}</div>
      `
      : ""
  }
      <div>${order?.shippingAddress?.city ?? ""}</div>
      <div>${order?.shippingAddress?.province ?? ""}</div>
      <div>${order?.shippingAddress?.country ?? ""}</div>
      <div>${order?.shippingAddress?.postcode ?? ""}</div>
      <div>${order?.shippingAddress?.phone ?? ""}</div>
    </div>

      <table class="table">
        <tr>
          <th></th>
          <th>DESCRIPTION</th>
          <th>QTY</th>
          <th style="">UNIT PRICE</th>
          <th>DISCOUNT</th>
          <th>AMOUNT(IDR)</th>
        </tr>
        ${renderOrderList()}
      </table>
  
 
        <div class="footer-section">
          ${
            !isDeals
              ? `<div style="display: flex; flex-direction: column; flex: 1">
               <div style="font-weight: bold">Terms & Conditions:</div>
               <ul>
                 <li>
                   Validity of this quotation is 10 days from the mentioned date
                   above
                 </li>
                 <li>
                   All prices stated in this quotation are including VAT 10% and
                   other applicable taxes
                 </li>
                 <li>
                   Prices stated in this quotation are excluding the Delivering
                   Charges
                 </li>
               </ul>
             </div>`
              : ""
          }
  
          <div style="display: flex; flex-direction: column; flex: 1">
            <div style="font-weight: bold">Terms of Payment:</div>
            <ul>
              <li>Ready Stock Items: Full payment before delivery</li>
              <li>
                Indent Items: 50% Down Payment is required upon confirmation and
                the remaining payment will be required prior to delivery
              </li>
              <li>
                No order cancellation after the quotation is confirmed and payment
                is received
              </li>
              <li>
                Please make your payment only to the following account:
                <ul>
                  <li>Bank: ${
                    company?.companyAccount?.bankName ?? "-"
                  } Account No: ${
    company?.companyAccount?.accountNumber ?? "-"
  }</li>
                  <li>Account name: ${
                    company?.companyAccount?.accountName ?? "-"
                  }</li>
                </ul>
              </li>
  
              <li>
                If you need further assistance, please do not hesitate to contact
                your sales representative
              </li>
            </ul>
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

const GeneratePdf = async (
  data: Order,
  user: User,
  company: Company,
  paymentList: Payment[],
  isDeals: boolean,
) => {
  try {
    const { uri } = await Print.printToFileAsync({
      html: await htmlContent(data, user, company, paymentList, isDeals),
      width: 793,
      height: 1122,
    })
    Sharing.shareAsync(uri).then((res) => {
      console.log(res, "quotation")
    })
  } catch (err) {
    toast("Maaf, gagal export. Mohon coba lagi.")
    console.error(err)
  }
}

export default GeneratePdf
