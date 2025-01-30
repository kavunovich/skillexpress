<?php
    require_once '../db/consts.php';
    require_once TokenDetectorUrl;
    require_once AuthManagerUrl;
    require_once NoticeManagerUrl;
    require_once DateTimeAgoUrl;

    $td = new TokenDetector();
    $am = new AuthManager();

    $is_im = true;
    $token = $td->getToken();
    $my_avatar = $am->getAvatarByToken($token);
    $my_username = $am->getUsernameByToken($token);

    if(isset($_GET['user']) && !empty(trim($_GET['user']))) {
        $token = $am->getTokenByUsername(trim($_GET['user']));
        $is_im = false;
    }

    if(isset($token)) {
        $first_name = $am->getFirstNameByToken($token) ?? '';
        $username = $am->getUsernameByToken($token) ?? '';
        $creation_date = $am->getCreationDateByToken($token) ?? 0;
        $avatar = $am->getAvatarByToken($token) ?? null;
    } else {
        $first_name = 'Неизвестно';
        $username = 'null';
        $creation_date = 0;
        $avatar = null;
    }
    
    $my_avatar = $avatar;

    if($my_username === $username) {
        $is_im = true;
    }

    if(!$td->exist() || empty($username)) {
        $td->goto($td::AUTH);
    }

    if(!$is_im) {
        $my_avatar = $am->getAvatarByToken($token);
    }

    $nm = new NoticeManager($username);
    $format_creation_date = date('j F Y г.', $creation_date);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $first_name; ?></title>

    <link rel="stylesheet" href="/index.css">
    <link rel="stylesheet" href="/defaults.css">
    <link rel="stylesheet" href="/navbar.css">
    <link rel="shortcut icon" href="/icons/favicon.svg" type="image/x-icon">
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
                    src="<?php echo $my_avatar; ?>"
                    onerror="this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP\/\/\/yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                    draggable="false"
                    alt="Profile"
                    title="Profile"
                    noselect 
                />
            </a>
        </navbar__inner>
    </navbar>

    <main nocenter>
        <main__inner>
            <profile>
                <profile__item>
                    <form action="/upload/?goto=profile" method="POST" enctype="multipart/form-data" class="form-avatar">
                    <?php echo $is_im ? '<input type="file" class="big-avatar" accept="image/*" name="avatar">' : ''; ?>
                        <img
                            class="big-avatar" 
                            src="<?php echo $avatar; ?>"
                            onerror="this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP\/\/\/yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                            draggable="false"
                            noselect 
                        />
                        <input type="hidden" name="<?php echo time();?>">
                    </form>
                </profile__item>
                <profile__item>
                    <div style="display: flex; align-items: start">
                        <h2>
                            <?php echo $first_name; ?>
                        </h2>

                        <username style="margin-left: 10px; margin-top: 4px;">
                            <?php echo "@$username"; ?>
                        </username>
                    </div>

                    <p>
                        <folder>Дата регистрации: </folder>
                        <?php echo $format_creation_date; ?>
                    </p>

                    <p>
                        <folder>Количество обьявлений: </folder>
                        <?php echo $nm->getCount() . " шт."; ?>
                    </p>

                    <?php
                        if($is_im) {
                            echo <<<END
                                <a href="/?logout=0">
                                    <exit style="margin: 10px 0">
                                        Выйти из учетной записи
                                    </exit>
                                </a>
                            END;
                        }
                    ?>

                </profile__item>
            </profile>

            <h1 folder><?php echo $is_im ? 'Мои Объявления' : 'Объявления'; ?></h1>
            <notice-main>
                <?php
                    foreach($nm->getNotices(0, 25, 'DESC') as $notice) {
                        $title = $notice['title'] ?? null;
                        $description = $notice['description'] ?? null;
                        $noticeid = $notice['noticeid'] ?? null;
                        $creation_date = $notice['creation_date'] ?? 0;
                        $photo = $notice['photo'] ?? null;

                        $price = $notice['price'] ?? 0;
                        $string_price = $price > 0 ? "<price>$price ₽</price> <o6>за час</o6>" : 'Бесплатно';

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
                                        <div style="display: flex;justify-content: space-between;align-items: center">
                                            <p>$string_price</p>
                                            <time>$format_creation_date назад</time>
                                        </div>
                                    </div>
                                </notice-main__inner>
                            </a>
                        EOT;
                    }
                ?>
            </notice-main>

            <?php
                if($is_im) {
                    echo <<<END
                        <a href="/addnotice/">
                            <add>+</add>
                        </a>
                    END;
                }
            ?>
        </main__inner>
    </main>

    <script>
        const pick_photo = document.querySelector('input[type="file"]');

        if(pick_photo) {
            pick_photo.addEventListener('change', () => {
                document.querySelector('.form-avatar').submit();
            });
        }
    </script>
</body>
</html>