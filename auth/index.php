<?php
    require_once '../db/consts.php';
    require_once AuthManagerUrl;
    require_once TokenDetectorUrl;

    $td = new TokenDetector();
    $am = new AuthManager();

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $token = $am->getTokenByUsername($username);
    $db_password = $am->getPasswordByUsername($username);

    $cookie_token = $am->getUsernameByToken($td->getToken());

    $error = '';

    if($td->exist() && !empty($cookie_token)) {
        $td->goto($td::MAIN);
    }

    if(empty(trim($username)) || empty(trim($password))) {
        $error = 'pass';
    } else {
        if ($db_password !== $password) {
            $error = 'pass';
        }
    }

    if(strlen($error) === 0 && isset($token)) {
        setcookie( "token", $token, time() + 1 * 365 * 24 * 60 * 60, '/');
        $td->goto($td::MAIN);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>

    <link rel="stylesheet" href="./index.css">
    <link rel="stylesheet" href="/defaults.css">
    <link rel="stylesheet" href="/navbar.css">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/x-icon">
</head>
<body>
    <navbar>
        <navbar__inner>
            <a href="/">
                <img class="logotype" draggable="false" src="/icons/logo_full.svg" alt="Skill Express" title="Skill Express" noselect />
            </a>
        </navbar__inner>
    </navbar>

    <main>
        <main__inner minimize>
            <h1>Авторизация в Skill Express</h1>
            <form method="post">
                <input type="text" name="username" placeholder="Логин" required autocomplete="off">
                <input type="password" name="password" placeholder="Пароль" required autocomplete="off">
                <input type="hidden" name="cache_buster" value="<?php echo time(); ?>">
                <button type="submit">Войти</button>
            </form>

            <a href="/register/" animate>Нет учетной записи? Регистрация</a>
        </main__inner>
    </main>
</body>
</html>