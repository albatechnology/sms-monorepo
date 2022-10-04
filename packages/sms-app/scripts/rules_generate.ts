// Replace res with the form rule to generate the Yup validation schema.
// This doesn't convert advanced types like enums so we'll need to change that manually.

const res = {
  data: [
    {
      key: "address_line_1",
      label: "address_line_1",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "required|string|min:5",
    },
    {
      key: "address_line_2",
      label: "address_line_2",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "nullable|string|min:5",
    },
    {
      key: "address_line_3",
      label: "address_line_3",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "nullable|string|min:5",
    },
    {
      key: "postcode",
      label: "postcode",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "nullable|string|min:2|max:10",
    },
    {
      key: "city",
      label: "city",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "nullable|string|min:2",
    },
    {
      key: "country",
      label: "country",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "nullable|string|min:2",
    },
    {
      key: "province",
      label: "province",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "nullable|string|min:2",
    },
    {
      key: "phone",
      label: "phone",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "nullable|string|min:5",
    },
    {
      key: "type",
      label: "type",
      formType: "text",
      dataFormat: "default",
      options: [
        {
          value: "address",
          label: "Address",
        },
        {
          value: "delivery",
          label: "Delivery",
        },
        {
          value: "billing",
          label: "Billing",
        },
      ],
      rule: "required|in:address,delivery,billing",
    },
    {
      key: "customer_id",
      label: "customer_id",
      formType: "text",
      dataFormat: "default",
      options: [],
      rule: "required|exists:customers,id",
    },
  ],
}
const snakeToCamel = (str) =>
  str.replace(/([-_]\w)/g, (g) => g[1].toUpperCase())

const result = res.data
  .map((x) => {
    const rules = x.rule.split("|")
    const isText = rules.includes("string") ? ".string()" : ""

    const minMatch = x.rule.match(/min:(\d+)/)
    const isMin = minMatch ? `.min(${minMatch[1]})` : ""
    const maxMatch = x.rule.match(/max:(\d+)/)
    const isMax = maxMatch ? `.max(${maxMatch[1]})` : ""

    const isOptional = rules.includes("nullable") ? ".optional()" : ""
    const isRequired = rules.includes("required")
      ? `.required("Mohon isi ${x.label}")`
      : ""

    return `${snakeToCamel(
      x.key,
    )}: Yup${isText}${isMin}${isMax}${isOptional}${isRequired},`
  })
  .join("\n")

console.log(result)
