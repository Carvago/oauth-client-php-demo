<?php
    session_start();

    $clientId = $_ENV['OAUTH_CLIENT_ID'] ?? '';
    $clientSecret = $_ENV['OAUTH_CLIENT_SECRET'] ?? '';
    $authServerUrl = $_ENV['OAUTH_AUTH_SERVER'] ?? 'https://api.stage.omnetic.dev';
    $redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . '/callback';
    $state = bin2hex(random_bytes(10)); // random string

    if (!isset($_GET['state'])) {
        $_SESSION['state'] = $state;
    }

    // Logout "route" - it is enough to delete access tokens from our session
    if (str_contains($_SERVER['REQUEST_URI'], '/logout')) {
        unset($_SESSION['access_token']);
        unset($_SESSION['refresh_token']);
    }

    // Refresh token "route"
    if (str_contains($_SERVER['REQUEST_URI'], '/refresh-token')) {
        if (isset($_SESSION['refresh_token'])) {
            $ch = curl_init();
            // POST request to obtain access token
            curl_setopt($ch, CURLOPT_URL, $authServerUrl . '/admin/oauth/access-token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $_SESSION['refresh_token'],
            ]);

            /** @var false|array{token_type: string, expires_in: int, access_token: string, refresh_token: string} $accessTokenData */
            $accessTokenData = json_decode(curl_exec($ch), true);
            $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

            // User is now signed in - we have both refresh and access token
            if ($responseCode === 200 && $accessTokenData) {
                $_SESSION['access_token'] = $accessTokenData['access_token'];
                $_SESSION['refresh_token'] = $accessTokenData['refresh_token'];
            }

            echo sprintf('<p><strong>Access token response:</strong> <pre>%s</pre></p>', print_r($accessTokenData, true));
        }
    }

    // Callback "route" - redirected back to exchange auth code for access token
    if (str_contains($_SERVER['REQUEST_URI'], '/callback')) {
        if (isset($_GET['code'], $_GET['state'])) {

            // Check against man in the middle attack (optional but recommended)
            if ($_GET['state'] !== $_SESSION['state']) {
                die('States do not match!!');
            }

            $ch = curl_init();
            // POST request to obtain access token
            curl_setopt($ch, CURLOPT_URL, $authServerUrl . '/admin/oauth/access-token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'grant_type' => 'authorization_code',
                'code' => $_GET['code'],
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
            ]);

            /** @var false|array{token_type: string, expires_in: int, access_token: string, refresh_token: string} $accessTokenData */
            $accessTokenData = json_decode(curl_exec($ch), true);
            $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

            // User is now signed in - we have both refresh and access token
            if ($responseCode === 200 && $accessTokenData) {
                $_SESSION['access_token'] = $accessTokenData['access_token'];
                $_SESSION['refresh_token'] = $accessTokenData['refresh_token'];
            }

            echo sprintf('<p><strong>Access token response:</strong> <pre>%s</pre></p>', print_r($accessTokenData, true));
        }
    }

    if (isset($_SESSION['access_token'])) {
        $ch = curl_init();
        // POST request to obtain access token
        curl_setopt($ch, CURLOPT_URL, $authServerUrl . '/admin/oauth/user');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            sprintf('Authorization: Bearer %s', $_SESSION['access_token']),
            'Accept: application/json',
            'Content-type: application/json',
        ]);

        /** @var array{id: string, email: string, firstName: string, lastName: string, phoneNumber: null|array{countryCode: string, prefix: string, number: string}} $userInfoData */
        $userInfoData = json_decode(curl_exec($ch), true);

        echo sprintf('<p><strong>Logged in as user:</strong> <pre>%s</pre></p>', print_r($userInfoData, true));
    }
?>


<?php
    if (!isset($_SESSION['access_token'], $_SESSION['refresh_token'])) {
?>
<p>
    <a href="<?php echo $authServerUrl; ?>/admin/oauth/authorize?response_type=code&state=<?php echo $state; ?>&client_id=<?php echo $clientId; ?>&redirect_uri=<?php echo $redirectUri ?>">
        Sign In
    </a>
</p>
<?php
    }

    if (isset($_SESSION['refresh_token'])) {
        echo '<p><a href="/refresh-token">Refresh token</a></p>';
    }

    if (isset($_SESSION['access_token'])) {
        echo '<p><a href="/logout">Log out</a></p>';
    }
