<?php
    $redirectUri = 'http://' . $_SERVER['HTTP_HOST'] . '/callback';
    $serverUrl = 'http://localhost:8080';
    $clientId = 'mikesId';
    $clientSecret = 'clientSecret';
    $state = base64_encode(random_bytes(10)); // random string

    // ulozit state do session

    if (isset($_GET['code'], $_GET['state'])) {
        // Check state, it should match

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $serverUrl . '/admin/oauth/access-token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'code' => $_GET['code'],
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        $data = curl_exec($ch);
?>

        <pre><?php print_r($data); ?></pre>

<?php
    }
?>

<a href="<?php echo $serverUrl; ?>/admin/oauth/authorize?response_type=code&state=<?php echo $state; ?>&client_id=<?php echo $clientId; ?>&redirect_uri=<?php echo $redirectUri ?>">
    Sign In
</a>

<a href="#">Log out</a>
