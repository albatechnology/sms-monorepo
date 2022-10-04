module.exports = function (api) {
  api.cache(true)

  return {
    presets: ["babel-preset-expo"],
    plugins: [
      [
        "module-resolver",
        {
          alias: {
            api: "./src/api",
            assets: "./src/assets",
            common: "./src/common",
            components: "./src/components",
            containers: "./src/containers",
            filters: "./src/filters",
            forms: "./src/forms",
            helper: "./src/helper",
            hooks: "./src/hooks",
            providers: "./src/providers",
            Router: "./src/Router",
            svg: "./src/svg",
            types: "./src/types",
            "react-native-web/dist/exports/AsyncStorage":
              "@react-native-community/async-storage",
            "react-native-web/dist/exports/ViewPropTypes":
              "./web/ViewPropTypes",
          },
        },
      ],
      "react-native-reanimated/plugin",
    ],
  }
}
