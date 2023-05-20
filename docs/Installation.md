# Installation

This document is an addendum to the [README](../README.md) file and covers advanced installation techniques. Please review the installation guidance there before continuing.

## Alternative Configuration Methods

Although our guidance is to use the Auth0 CLI to create the JSON configuration files for the SDK, we recognize that this may not be possible in all cases.

### Environment Variables

The SDK supports the use of environment variables to configure the SDK. These can be defined in the `.env` file in the root of your project, or in your hosting environment.

To successfully use the SDK, you must provide the following environment variables at a minimum:

| Variable              | Description                             |
| --------------------- | --------------------------------------- |
| `AUTH0_DOMAIN`        | The Auth0 domain for your tenant.       |
| `AUTH0_CLIENT_ID`     | The Client ID for your application.     |
| `AUTH0_CLIENT_SECRET` | The Client Secret for your application. |

For a full list of supported environment variables, see [Configuration](./Configuration.md).
