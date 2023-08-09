# Sessions

In order to persist users' authentication states between HTTP requests, the Auth0 Laravel SDK uses Laravel's Session API to store and retrieve necessary data about the user. Applications can configure Laravel's Session API by modifying their `config/session.php` file. By default, sessions use the `file` driver, which stores the serialized user information in a file on the application server. However, you can configure the session store to use any of the other session drivers, such as `cookie`, `database`, `apc`, `memcached` and `redis`.

It's important to note that all session drivers, except for `cookie`, require server-side storage of the session data. If you are using a load balancer or other server cluster, you must use a session driver that is shared across all of the servers.

We strongly recommend using the `database` or `redis` session drivers for applications that use Auth0. These drivers are the most reliable and scalable options for storing session data.

## Files

The default session driver is `file`, which stores the session data in files on the server. It works well for simple applications, but it does not scale reliably beyond a single server.

## Cookies

The `cookie` session driver stores the session data in secure, encrypted cookies on the client device. Although convenient, this approach is not a reliable option for production applications as it suffers from a number of notable drawbacks:

- Browsers impose a size limit of 4 KB on individual cookies, which can quickly be exceeded by storing session data.
- Laravel's cookie driver unfortunately does not "chunk" (split up) larger cookies into multiple cookies, so it is impossible to store more than the noted 4 KB of total session data.
- Most web servers and load balancers require additional configuration to accept and deliver larger cookie headers.

If your application requires the use of cookies, please use the Auth0 PHP SDK's custom cookie session handler instead. This approach supports chunking of larger cookies, but is notably incompatible with [Octane](./Octane.md). Please refer to [Cookies.md](./Cookies.md) for more information.

## Database

The `database` session driver stores the session data in a database table. This is a very reliable option for applications of any size, but it does require a database connection to be configured for your application.

## Redis

The `redis` session driver stores the session data in a Redis database. This is an equally reliable option to the `database` driver.

## APC

The `apc` session driver stores the session data in the APC cache. This is a very fast and reliable option for applications of any size, but it does require the APC PHP extension to be installed on your server.

## Memcached

The `memcached` session driver stores the session data in a Memcached database. This is an equally reliable option to the `apc` driver, but it does require the Memcached PHP extension to be installed on your server.

## Array (Testing)

The `array` session driver stores the session data in a PHP array. This option is generally used for running tests on your application as it does not persist session data between requests.
