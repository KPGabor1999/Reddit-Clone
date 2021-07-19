<?php

//Használni kívánt PHP modulok importálása:

//*Ne felejtsd el inicializálni az adatbázist.*

//A komponensek által közösen használt hibagyűjtő tömb.
//Még az importálások előtt legyen kirakva!
$errors = [];                                   //A különbőző modulok közösen ide gyűjtik a felmerülő validálási hibáikat.

//A főprogramban használt modulok importálása
require_once('assets/utils/verify_keys_exist.php');		// ../helyett egyszerű helyettesítési elv
require_once('assets/utils/Storage.php');
require_once('assets/utils/Auth.php');
require_once('assets/validators/register_login.php');  //Főprogramba importáláshoz require_once
require_once('assets/validators/post_upvote_comment.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!--A főoldalon használatos CSS osztályok-->
    <style>
    small.error{
        color: red;
    }
    small.success{
        color: green;
    }
    </style>
</head>
<body>
    <!--Regisztrációs form-->
    <h2>Regisztráció</h2>
    <form action="" method="post">
        Felhasználónév: <input type="text" name="username_register" value="<?=$_POST['username_register'] ?? ''?>"><br>
        Jelszó: <input type="text" name="password_register" value="<?=$_POST['password_register'] ?? ''?>"><br>
        <?php if(isset($errors['invalid_register'])): ?>
            <small class="error"><?= $errors['invalid_register']?></small></br>
        <?php endif ?>
        <?php if(isset($errors['empty_username_or_password'])): ?>
            <small class="error"><?= $errors['empty_username_or_password']?></small></br>
        <?php endif ?>
        <button type="submit">Regisztráció</button>
    </form>

    <!--Bejelentkező form-->
    <h2>Bejelentkezés</h2>
    <form action="" method="get">
        Felhasználónév: <input type="text" name="username_login" value="<?=$_GET['username_login'] ?? ''?>"><br>
        Jelszó: <input type="text" name="password_login" value="<?=$_GET['password_login'] ?? ''?>"><br>
        <?php if(isset($errors['invalid_login'])): ?>
            <small class="error"><?= $errors['invalid_login']?></small></br>
        <?php endif ?>
        <button type="submit">Bejelentkezés</button>
    </form>

    <!--Posztokat elküldő form-->
    <?php if(isset($_SESSION['user'])): ?>
    <form action="" method="post">
        <h2>Új poszt létrehozása</h2>
        <?php if(isset($errors['invalid_post'])): ?>
            <small class="error"><?= $errors['invalid_post']?></small></br>
        <?php endif ?>
        <input type="hidden" name="type" value="post">
        PosztID:<input type="text" name="postID"></br>
        Cím:<input type="text" name="title"></br>
        Leírás:<input type="text" name="description"></br>
        <input type="hidden" name="username" value="<?= $_SESSION['user']?>">
        Subreddit neve:<input type="text" name="subreddit"></br>
        <input type="hidden" name="datetime" value="<?= date("Y.m.d h:i:sa")?>">
        <button type="submit">Kiposztol</button>
    </form>
    <?php endif ?>

    <!--Posztok és kommentjeik kiírása az oldalra-->
    <?php foreach($post_storage->findAll() as $post): ?>
        <p>
            <span style="color: gray; font-size: 12px"> #<?= $post['postID']?> Posted by <?= $post['username']?> on <?= $post['subreddit']?>
             at <?= $post['datetime']?> (upvotes:<?= $post['upvotes']?>, 
             trophies: *folyt. köv.*)</span></br>
            <h3><?= $post['title']?></h3>
            <span><?= $post['description']?></span></br></br>
            <button name="upvoteBtn" data-post-id="<?= $post['postID']?>">Upvote</button>
            <?php foreach($post['comments'] as $comment): ?>
                <div style="margin-left: 50px">
                    <span style="color: gray; font-size: 12px">#<?= $comment['commentID']?> Commented by <?= $comment['username']?>
                     at <?= $comment['datetime']?> (upvotes: <?= $comment['upvotes']?>, trophies: *folyt. köv.*)</span></br>
                    <span><?= $comment['message']?></span></br></br>
                </div>
            <?php endforeach?>
        </p>
    <?php endforeach?>

    <!--Kommenteket elküldő form-->
    <?php if(isset($_SESSION['user'])): ?>
    <form action="" method="post">
        <h2>Új komment hozzáfűzése</h2>
        <?php if(isset($errors['invalid_comment'])): ?>
            <small class="error"><?= $errors['invalid_comment']?></small></br>
        <?php endif ?>
        <input type="hidden" name="type" value="comment">
        PosztID:<input type="text" name="postID"></br>
        KommentID:<input type="text" name="commentID"></br>
        Üzenet:<input type="text" name="message"></br>
        <input type="hidden" name="username" value="<?= $_SESSION['user']?>">
        <input type="hidden" name="datetime" value="<?= date("Y.m.d h:i:sa")?>">
        <button type="submit">Hozzáfűz</button>
    </form>
    <?php endif ?>

    <script src="upvote.js"></script>
</body>
</html>