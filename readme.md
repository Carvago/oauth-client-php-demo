# OAuth 2 client for OMNETIC DMS

## Usage

You will need to obtain client id + client secret from service provider.

Then, start PHP server, with obtained credentials as environment variables (replace with yours):
```
OAUTH_CLIENT_ID='<my-client-id>' OAUTH_CLIENT_SECRET='<my-client-secret>' php -S 0.0.0.0:8080 -t public
```

Now access `localhost:8080` in your browser (the URL depends on what you run in the command, you can adapt it to your needs).

You can use optional variable `OAUTH_AUTH_SERVER` to change auth server from default `https://api.dev.omnetic.dev`.

You can use optional variable `OAUTH_CALLBACK_PATH` (defaults to `callback`) to change redirect_uri which is sent to authorization server:
```
$redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . '/' . ($_ENV['CALLBACK_PATH'] ?? 'callback');
```

## Requirements

- Requires PHP >= 8.0
- cURL extension

---

<img width="500" alt="page" src="https://github.com/Carvago/oauth-client-php-demo/raw/main/docs/page.png">
