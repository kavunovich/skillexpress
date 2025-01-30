<?php
    require_once '../db/consts.php';
    require_once vendor;
    require_once TokenDetectorUrl;
    require_once AuthManagerUrl;
    require_once NoticeManagerUrl;
    require_once DateTimeAgoUrl;

    use cebe\markdown\Markdown;

    $td = new TokenDetector();
    $am = new AuthManager();
    $markdown = new Markdown();

    if(!$td->exist()) {
        $td->goto($td::AUTH);
    }

    $token = $td->getToken();
    $my_username = $am->getUsernameByToken($token);
    $id = $_GET['id'] ?? null;

    if(isset($id) && !empty(trim($id))) {
        if(!$td->exist() || empty($am->getUsernameByToken($token))) {
            $td->goto($td::AUTH);
        }
    
        $nm = new NoticeManager('null');
        $notice = $nm->getNotice($id);
    }

    $title = $notice[0]['title'] ?? 'Unknown';
    $photo = $notice[0]['photo'] ?? null;
    $description = $notice[0]['description'] ?? 'Null';
    $price = $notice[0]['price'] ?? 0;
    $username = $notice[0]['username'] ?? 'null';

    $first_name = $am->getFirstNameByUsername($username) ?? 'Unknown';
    $notice_avatar = $am->getAvatarByUsername($username) ?? null;
    $avatar = $am->getAvatarByToken($token) ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

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
            <profile>
                <profile__item>
                    <img
                        class="big-avatar" 
                        src="<?php echo $photo; ?>"
                        draggable="false"
                        noselect
                        onerror="this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP\/\/\/yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                    />
                </profile__item>
                <profile__item>
                    <div style="display: flex; align-items: center">
                        <h2>
                            <?php echo $title; ?>
                        </h2>
                    </div>

                    <p>
                        <?php echo $price > 0 ? "<price>$price ₽</price> <o6>за час</o6>" : 'Бесплатно'; ?>
                    </p>

                    <a href="/profile/?user=<?php echo $username; ?>">
                        <user>
                            <img 
                                class="avatar" 
                                src="<?php echo $notice_avatar; ?>"
                                onerror="this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP\/\/\/yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                                draggable="false"
                                alt="Profile"
                                title="Profile"
                                noselect 
                            />
                            <?php echo $first_name; ?>
                        </user>
                    </a>

                    <?php
                        if($username === $my_username) {
                            echo <<<END
                                <a href="/upload/?goto=deletenotice&noticeid=$id">
                                    <exit>
                                        Удалить объявление
                                    </exit>
                                </a>
                            END;
                        }
                    ?>
                </profile__item>
            </profile>

            <markdown>
                <desc><?php echo $markdown->parse($description); ?></desc>
            </markdown>
        </main__inner>
    </main>
</body>
</html>