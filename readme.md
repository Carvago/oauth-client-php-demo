# OAuth 2 client for OMNETIC DMS

## Usage

*Note: V2 supports only JSON request body and properties are camelCase. To switch to v1 which uses multipart/form-data requests, properties are in snake_case and different endpoints replace v2 with v1 in the start server command below.*

You will need to obtain client id + client secret from service provider.

Then, start PHP server, with obtained credentials as environment variables (replace with yours):
```
OAUTH_CLIENT_ID='<my-client-id>' OAUTH_CLIENT_SECRET='<my-client-secret>' php -S 0.0.0.0:8080 -t ./v2
```

Now access `localhost:8080` in your browser (the URL depends on what you run in the command, you can adapt it to your needs).

You can use optional variable `OAUTH_AUTH_SERVER` to change auth server from default `https://api.dev.omnetic.dev`.

You can use optional variable `OAUTH_CALLBACK_PATH` (defaults to `callback`) to change redirect_uri which is sent to authorization server:
```
$callbackPath = $_ENV['OAUTH_CALLBACK_PATH'] ?? 'callback';
$redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $callbackPath;
```

## Requirements

- Requires PHP >= 8.0
- cURL extension

---

<img width="500" alt="page" src="https://github.com/Carvago/oauth-client-php-demo/raw/main/docs/page.png">  
<img width="500" alt="page" src="https://github.com/Carvago/oauth-client-php-demo/raw/main/docs/doc-user-info.png">
<img width="500" alt="page" src="https://github.com/Carvago/oauth-client-php-demo/raw/main/docs/doc-access-token.png">
<img width="500" alt="page" src="https://github.com/Carvago/oauth-client-php-demo/raw/main/docs/doc-refresh-token.png">
