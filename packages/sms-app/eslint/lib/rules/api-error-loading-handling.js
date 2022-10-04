// Complete rule
module.exports = {
  create(context) {
    return {
      CallExpression(node) {
        if (node.callee.name && node.callee.name === "useMultipleQueries") {
          if (
            node.parent.type === "VariableDeclarator" &&
            node.parent.id.type === "ObjectPattern"
          ) {
            const objectPattern = node.parent.id

            const metaProperty = objectPattern.properties.find(
              (p) => p.key.name === "meta",
            )

            if (!metaProperty) {
              return context.report({
                node: objectPattern,
                message: `"meta" object from useMultipleQueries should be used`,
              })
            }

            if (metaProperty.value.type === "ObjectPattern") {
              const metaSpread = metaProperty.value
              const isErrorProperty = metaSpread.properties.find(
                (p) => p.key.name === "isError",
              )

              if (!isErrorProperty) {
                return context.report({
                  node: metaSpread,
                  message: `'isError' property from useMultipleQueries should be used`,
                })
              }

              const isLoadingProperty = metaSpread.properties.find(
                (p) => p.key.name === "isLoading",
              )

              if (!isLoadingProperty) {
                return context.report({
                  node: metaSpread,
                  message: `'isLoading' property from useMultipleQueries should be used`,
                })
              }
            }
          }
        }
        return null
      },
    }
  },
}
