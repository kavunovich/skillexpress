<?php
    require_once '../db/consts.php';
    require_once TokenDetectorUrl;
    require_once AuthManagerUrl;
    require_once NoticeManagerUrl;
    require_once ImageUploaderUrl;

    $td = new TokenDetector();
    $am = new AuthManager();
    $goto = $_GET['goto'] ?? null;

    if(!$td->exist()) {
        $td->goto($td::AUTH);
    }
    
    if($goto === 'profile') {
        if(!empty($_FILES)) {
            try {
                $uploader = new ImageUploader(photosPath);
                $uploadedFile = $uploader->upload('avatar');
                $username = $am->getUsernameByToken($td->getToken());

                $am->setAvatar(substr($uploadedFile, 2, strlen($uploadedFile)), $username);
                $td->goto($td::PROFILE);
            } catch (Exception $e) {
                echo "Ошибка: " . $e->getMessage();
            }
        }

    }

    if($goto === 'addnotice') {
        $username = $am->getUsernameByToken($td->getToken());
        $nm = new NoticeManager($username);

        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        $uploader = new ImageUploader(photosPath);
        $uploadedFile = $uploader->upload('photo');

        $nm->addNotice($title, $description, $uploadedFile, $price);
        $td->goto($td::PROFILE);
    }

    if($goto === 'deletenotice') {
        $id = $_GET['noticeid'] ?? null;

        $username = $am->getUsernameByToken($td->getToken());
        $nm = new NoticeManager($username);
        $notice = $nm->getNotice($id);

        if(!isset($notice[0]['username']) || $notice[0]['username'] !== $username) {
            $td->goto($td::MAIN);
            die();
        }

        $nm->deleteNotice($id);
        $td->goto($td::MAIN);
    }