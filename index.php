<?php
    require_once 'db/consts.php';
    require_once TokenDetectorUrl;
    require_once AuthManagerUrl;
    require_once NoticeManagerUrl;
    require_once DateTimeAgoUrl;

    $td = new TokenDetector();
    $am = new AuthManager();

    if(isset($_GET['logout'])) {
        setcookie('token', '', -1, '/');
        $td->goto($td::AUTH);
    }

    $token = $td->getToken();

    if(!$td->exist() || empty($am->getUsernameByToken($token))) {
        $td->goto($td::AUTH);
    }

    $nm = new NoticeManager('null');
    $avatar = $am->getAvatarByToken($token) ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Express</title>

    <link rel="stylesheet" href="./index.css">
    <link rel="stylesheet" href="/defaults.css">
    <link rel="stylesheet" href="/navbar.css">
    <link rel="shortcut icon" href="icons/favicon.svg" type="image/x-icon">
</head>
<body>
    <navbar>
        <navbar__inner style="justify-content: space-between">
            <a href="/">
                <img
                    class="logotype"
                    draggable="false"
                    src="/icons/logo_full.svg"
                    alt="Skill Express"
                    title="Skill Express" 
                    noselect 
                />
            </a>

            <a href="/profile/">
                <img 
                    class="avatar" 
                    src="<?php echo $avatar; ?>"
                    onerror="this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP\/\/\/yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                    draggable="false"
                    alt="Profile"
                    title="Profile"
                    noselect 
                />
            </a>
        </navbar__inner>
    </navbar>

    <main>
        <main__inner>
            <notice-main>
                <?php
                    foreach($nm->getAllNotices(0, 25, 'DESC') as $notice) {
                        $status = $notice['status'] ?? 0;

                        if($status === 0) {
                            continue;
                        }

                        $title = $notice['title'] ?? null;
                        $description = $notice['description'] ?? null;
                        $noticeid = $notice['noticeid'] ?? null;
                        $username = $notice['username'] ?? null;
                        $creation_date = $notice['creation_date'] ?? 0;
                        $price = $notice['price'] ?? 0;
                        $string_price = $price > 0 ? "<price>$price ₽</price> <o6>за час</o6>" : 'Бесплатно';

                        $first_name = $am->getFirstNameByUsername($username) ?? 'Неизвестно';
                        $photo = $notice['photo'] ?? null;

                        $format_creation_date = DateTimeAgo::format($creation_date);

                        echo <<<EOT
                            <a href="/notice/?id=$noticeid">
                                <notice-main__inner>
                                    <img 
                                        class="big-avatar" 
                                        src="$photo"
                                        draggable="false"
                                        noselect
                                        onerror="this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                                    />
                                    <div>
                                        <h3>$title</h3>
                                        <p style="height: 25px">$string_price</p>
                                        <bottominfo>
                                            <firstname style="margin-right: 5px">$first_name</firstname>
                                            <time>$format_creation_date назад</time>
                                        </bottominfo>
                                    </div>
                                </notice-main__inner>
                            </a>
                        EOT;
                    }
                ?>
            </notice-main>
        </main__inner>
    </main>
</body>
</html>