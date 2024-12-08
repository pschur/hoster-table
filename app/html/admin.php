<?php

require __DIR__.'/../server.php';

if (!check_auth()) {
    redirect('/auth.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go to Admin</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <main class="container">
        <form action="<?= $_ENV['ADMIN_URL'] ?>" method="post">
            <input type="hidden" name="auth[driver]" value="server">
            <input type="hidden" name="auth[server]" value="<?= $_ENV['DB_HOSTNAME'] ?>">
            <input type="hidden" name="auth[username]" id="username" value="<?= $_ENV['DB_USERNAME'] ?>">
            <input type="hidden" name="auth[password]" value="<?= $_ENV['DB_PASSWORD'] ?>">
            <input type="hidden" name="auth[db]"  value="<?= $_ENV['DB_DATABASE'] ?>">

            <button type="submit">Login to Admin</button>
        </form>
    </main>
</body>
</html>