const fs = require("fs/promises")

;(async () => {
  const fileString = await fs.readFile("../../contracts/enums.json", {
    encoding: "utf-8",
  })
  const enumJson = JSON.parse(fileString)

  const formattedString = enumJson
    .map((enumData) => {
      return `export const ${enumData.code}List = ${JSON.stringify(
        enumData.enums.map((enm) => enm.value),
      )}
export type ${enumData.code} = ${enumData.enums
        .map((enm) => `"${enm.value}"`)
        .join(" | ")}
export const ${enumData.code}ReadOnlyList = ${JSON.stringify(
        enumData.enums.filter((enm) => enm.read_only).map((enm) => enm.value),
      )}`
    })
    .join("\n\n")

  await fs.writeFile("./src/api/generated/enums.ts", formattedString, {
    encoding: "utf-8",
  })
})()
