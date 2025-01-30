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

    if(!$td->exist()) {
        $td->goto($td::AUTH);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Express</title>

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
        <main__inner minimize>
            <form action="/upload/?goto=addnotice" method="POST" enctype="multipart/form-data">
                <profile>
                    <profile__item>
                        <input type="file" class="big-avatar" accept="image/*" name="photo" required>
                        <img
                            class="big-avatar" 
                            src="null"
                            onerror="this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP\/\/\/yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'"
                            draggable="false"
                            noselect 
                        />
                        <input type="hidden" name="<?php echo time();?>">
                    </profile__item>
                </profile>

                <input type="text" name="title" placeholder="Наименование обьявления" required>
                <textarea name="description" placeholder="Описание обьявления" required></textarea>
                <input type="number" name="price" placeholder="0 ₽ / час" min="0" max="999999999" required>
                <button type="submit">Опубликовать</button>
            </form>
        </main__inner>
    </main>

    <script>
        const pick_photo = document.querySelector('input[type="file"]');
        const pick_photo_img = document.querySelector('img.big-avatar');
        if(pick_photo) {
            pick_photo.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const objectURL = URL.createObjectURL(file);

                pick_photo_img.src = objectURL;
            });
        }
    </script>
</body>
</html>