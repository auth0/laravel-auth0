# Deployment

When you're ready to deploy your application to production, there are some important things you can do to make sure your application is running as efficiently as possible. In this document, we'll cover some great starting points for making sure your Laravel application is deployed properly.

- [Auth0 Configuration](#auth0-configuration)
- [TLS / HTTPS](#tls--https)
- [Cookies](#cookies)
- [Server Configuration](#server-configuration)
  - [Caddy](#caddy)
  - [Nginx](#nginx)
  - [Apache](#apache)
- [Optimization](#optimization)
  - [Autoloader](#autoloader)
  - [Dependencies](#dependencies)
  - [Caching Configuration](#caching-configuration)
  - [Caching Events](#caching-events)
  - [Caching Routes](#caching-routes)
  - [Caching Views](#caching-views)
  - [Debug Mode](#debug-mode)

## Auth0 Configuration

When migrating your Laravel application from local development to production, you will need to update your Auth0 application's configuration to reflect the new URLs for your application. You can do this by logging into the [Auth0 Dashboard](https://manage.auth0.com/) and updating the following fields:

- **Allowed Callback URLs**: The URL that Auth0 will redirect to after the user authenticates. This should be set to the Internet-accessible URL of your application's `/callback` route.
- **Allowed Logout URLs**: The URL that Auth0 will redirect to after the user logs out. This should be set to an appropriate Internet-accessible URL of your application.

Note that you can include multiple URLs in these fields by separating them with commas, for example:

```
https://example.com/callback,http://localhost:8000/callback
```

See [the configuration guide](/docs/configuration.md) for additional guidance on updating configuration properties.

## TLS / HTTPS

Auth0 requires that all applications use TLS/HTTPS. This is a requirement for all applications, regardless of whether they are running in production or development, with the exception of applications running on `localhost`. If you are running your application in a development environment, you can use a self-signed certificate. However, you should ensure that your application is running over TLS/HTTPS in production.

Let's Encrypt is a great option for obtaining free TLS/HTTPS certificates for your application. You can find instructions for obtaining a certificate for your server at [https://letsencrypt.org/getting-started/](https://letsencrypt.org/getting-started/).

## Cookies

Depending on the integration approach, you may encounter instances where the cookies delivered by the application exceed the default allowances of your web server. This can result in errors such as `400 Bad Request`. If you encounter this issue, you should increase the header size limits of your web server to accommodate the larger cookies. The server configurations below include examples of how to do this for common web servers.

You should also ensure your application's `config/session.php` file is configured securely. The default configuration provided by Laravel is a great starting point, but you should double check that the `secure` option is set to `true`, that the `same_site` option is set to `lax` or `strict`, and the `http_only` option is set to `true`.

## Server Configuration

Please ensure, like all the example configurations provided below, that your web server directs all requests to your application's `public/index.php` file. You should **never** attempt to move the `index.php` file to your project's root, as serving the application from the project root will expose many sensitive configuration files to the public Internet.

### Caddy

```nginx
example.com {
    root * /var/www/example.com/public

    encode zstd gzip
    file_server

    limits {
        header 4kb
    }

    header {
        X-XSS-Protection "1; mode=block"
        X-Content-Type-Options "nosniff"
        X-Frame-Options "SAMEORIGIN"
    }

    php_fastcgi unix//var/run/php/php8.1-fpm.sock
}
```

### Nginx

```nginx
server {
  listen 80;
  listen [::]:80;
  server_name example.com;
  root /var/www/example.com/public;
 
  add_header X-XSS-Protection "1; mode=block";
  add_header X-Content-Type-Options "nosniff";
  add_header X-Frame-Options "SAMEORIGIN";

  large_client_header_buffers 4 32k;
 
  index index.php;
 
  charset utf-8;
 
  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }
 
  location = /favicon.ico { access_log off; log_not_found off; }
  location = /robots.txt  { access_log off; log_not_found off; }
 
  error_page 404 /index.php;
 
  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
  }
 
  location ~ /\.(?!well-known).* {
    deny all;
  }
}
```

### Apache

```apache
<VirtualHost *:80>
  ServerName example.com
  ServerAdmin admin@example.com
  DocumentRoot /var/www/html/example.com/public

  LimitRequestFieldSize 16384

  <Directory /var/www/html/example.com>
    AllowOverride All
  </Directory>

  <IfModule mod_headers.c>
    Header set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options SAMEORIGIN
  </IfModule>

  ErrorLog ${APACHE_LOG_DIR}/error.log
  CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

## Optimization

### Autoloader

When deploying to production, make sure that you are optimizing Composer's class autoloader map so Composer can quickly find the proper file to load for a given class:

```shell
composer install --optimize-autoloader --no-dev
```

Be sure to use the `--no-dev` option in production. This will prevent Composer from installing any development dependencies your project's dependencies may have.

### Dependencies

You should include your `composer.lock` file in your project's source control repository. Fo project's dependencies can be installed much faster with this file is present. Your production environment does not run `composer update` directly. Instead, you can run the `composer update` command locally when you want to update your dependencies, and then commit the updated `composer.lock` file to your repository. Be sure you are running the same major PHP version as your production environment, to avoid introducing compatibility issues.

Because the `composer.lock` file includes specific versions of your dependencies, other developers on your team will be using the same versions of the dependencies as you. This will help prevent bugs and compatibility issues from appearing in production that aren't present during development.

### Caching Configuration

When deploying your application to production, you should make sure that you run the config:cache Artisan command during your deployment process:

```shell
php artisan config:cache
```

This command will combine all of Laravel's configuration files into a single, cached file, which greatly reduces the number of trips the framework must make to the filesystem when loading your configuration values.

### Caching Events

If your application is utilizing event discovery, you should cache your application's event to listener mappings during your deployment process. This can be accomplished by invoking the event:cache Artisan command during deployment:

```shell
php artisan event:cache
```

### Caching Routes

If you are building a large application with many routes, you should make sure that you are running the route:cache Artisan command during your deployment process:

```shell
php artisan route:cache
```

This command reduces all of your route registrations into a single method call within a cached file, improving the performance of route registration when registering hundreds of routes.

### Caching Views

When deploying your application to production, you should make sure that you run the view:cache Artisan command during your deployment process:

```shell
php artisan view:cache
```

This command precompiles all your Blade views so they are not compiled on demand, improving the performance of each request that returns a view.

## Debug Mode

The debug option in your `config/app.php` configuration file determines how much information about an error is actually displayed to the user. By default, this option is set to respect the value of the `APP_DEBUG` environment variable, which is stored in your application's .env file.

**In your production environment, this value should always be `false`. If the `APP_DEBUG` variable is set to `true` in production, you risk exposing sensitive configuration values to your application's end users.**
