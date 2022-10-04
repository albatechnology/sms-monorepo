const fs = require("fs/promises")

;(async () => {
  await fs.copyFile(
    "../../contracts/errors.json",
    "./src/api/generated/errors.json",
  )
})()
