# Laravel SDK API Usage Examples

You can execute any of the following examples from your command line to see
what a raw API response from the Laravel SDK API example looks like.

## `auth0.authorize.optional` middleware

This middleware handles tokens when provided, but they are not required.
This is useful for routes that can show different results, depending on
whether the request is authorized or not.

### Public route, no token (OK)

curl 'http://localhost:8000/auth0' \
 -H 'Accept: application/json' \
 --compressed

### Public route, with a token (OK)

curl 'http://localhost:8000/auth0' \
 -H 'Accept: application/json' \
 -H 'Authorization: Bearer %TOKEN%' \
 --compressed

## `auth0.authorize` middleware

This middleware requires a valid token to be present in the request, or
or the request will be denied. This is useful for routes that require
explicit authorization.

### Protected route, no token (Unauthorized)

curl 'http://localhost:8000/auth0/private' \
 -H 'Accept: application/json' \
 --compressed

### Protected route, with a token (OK)

curl 'http://localhost:8000/auth0/private' \
 -H 'Accept: application/json' \
 -H 'Authorization: Bearer %TOKEN%' \
 --compressed

## `auth0.authorize` middleware with scopes

As a further extension of the `auth0.authorize` middleware, you can also
require that the token has specific scopes. This is useful for routes
that require explicit authorization, but only for specific actions.

### Protected route, no token (Unauthorized)

curl 'http://localhost:8000/auth0/private-scope' \
 -H 'Accept: application/json' \
 --compressed

### Protected route, with a token + matching scope (OK)

curl 'http://localhost:8000/auth0/private-scope' \
 -H 'Accept: application/json' \
 -H 'Authorization: Bearer %TOKEN%' \
 --compressed

### Protected route, with a token, but no matching scope (Forbidden)

curl 'http://localhost:8000/auth0/private-another-scope' \
 -H 'Accept: application/json' \
 -H 'Authorization: Bearer %TOKEN%' \
 --compressed
