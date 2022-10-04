# melandas-app

This is mobile app application for POS melandas

## Getting started

Try to use `yarn` instead of `npm` if possible

- Clone repo
- `yarn`
- `yarn start`

## Project Structure

The main app lives inside the `src` folder. Here are a few important folders:

- `api` - Stores each API endpoints as a React Hook.
- `components` - React component we can reuse in different parts.
- `containers` - Currently hold Screens and anything it uses. We might move the Screens to another folder in the future.
- `forms` - Holds reuseable form components.
- `helper` - Common functions.
- `hooks` - Common react hooks.
- `providers` - React context files. These are for global state.
- `Router` - Anything related to routing.
- `types` - Hold all internal types we use. Also contains common functions relating to the type.

## API

We have OpenAPI specification from the backend. We use that to generate the api client using `openapi-generator-cli`.
Generated client can be found on `src/api/openapi/`

We can update it by running:

- `yarn openapi`

There is also some generated data shared from the backend that we can generate. These can be generated using
- `yarn generate-contracts`

To run everything, simply run 
- `yarn generate` 


## Prettier

We can run prettier on all the file by running
- `yarn prettier`

## Running in Prod

Read https://docs.expo.io/distribution/release-channels/ since we're using this framework.

We're using 2 channels:

- `prod` for production build
- `default` for staging (and dev)
