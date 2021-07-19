<?php

//Posztok validálása, elmentése rekordként és upvote-olásuk:

//Szükséges segédosztályok importálása
//require_once('../utils/verify_keys_exist.php');      //A komponensekben csak include_once legyen
//require_once('../utils/Storage.php');

//A posztokat tároló 'adatbázis'
$post_storage = new Storage(new JsonIO('assets/json_databases/posts.json'));         //Json tároló szabályos létrehozása

//Elküldött poszt validálása és mentése
if(count($_POST) > 0){
    if(isset($_POST['type']) && $_POST['type'] === 'post'){                              //Ha posztot küldtünk,
        if(verify_post('postID', 'title', 'description', 'username', 'subreddit')){      //és megkaptunk minden szükséges adatot a formtól,
            if(
                trim($_POST['postID'])      !== '' &&                                    //és nem voltak üres mezők.
                trim($_POST['title'])       !== '' &&
                trim($_POST['description']) !== '' &&
                trim($_POST['username'])    !== '' &&
                trim($_POST['subreddit'])   !== ''
            ){                                                                           //Új mező hozzáadása:
                $post_storage->add(                                                      //A -> operátorral meghívod az add() metódust, amiben felparaméterezed a létrehozandó rekordot.
                    [
                        'postID' => $_POST['postID'],
                        'title' => $_POST['title'],
                        'description' => $_POST['description'],
                        'username' => $_POST['username'],
                        'subreddit' => $_POST['subreddit'],
                        'datetime' => $_POST['datetime'],
                        'trophies' => [],
                        'upvotes' => 0,
                        'comments' => []
                    ]
                );
            } else {
                $errors['invalid_post'] = "Érvénytelen poszt! Próbáld újra!";
            }
        }
    }
}

//Már létező posztok upvote-olása
if(isset($_GET['postId'])){
    $requested_post = $post_storage->findOne(['postID' => $_GET['postId']]);      //lekéred a posztot az id alapján
    $requested_post['upvotes']++;                                                 //növeled az upvote-jait eggyel
    $post_storage->update($requested_post['id'], $requested_post);                //frissíted a posts.json-t

    echo(json_encode($requested_post));                                           //visszaküldöd a frissített rekordot a JavaScript-nek
}

//Elküldött komment validálása és hozzáfűzése már kirakott posztokhoz
if(count($_POST) > 0){
    if(isset($_POST['type']) && $_POST['type'] === 'comment') {                              //Ha kommentet küldtünk,
        if(verify_post('postID', 'commentID', 'message', 'username')){                       //és megaptuk a szükséges adatokat,
            if(
                strlen($_POST['postID'])    > 0 &&                                           //és nem voltak üresek a mezők.
                strlen($_POST['commentID']) > 0 &&
                strlen($_POST['message'])   > 0 &&
                strlen($_POST['username'])  > 0
            ){                                                                               //Rekordok frissítése:
                $requested_post = $post_storage->findOne(['postID' => $_POST['postID']]);    //1. Előveszed a frissítendő rekordot.
                
                if($requested_post !== NULL){
                    $requested_post['comments'][] = [                                        //2. Ha létezik a rekord, frissíted a belső állapotát egy vagy több helyen.
                        'commentID' => $_POST['commentID'],
                        'message'   => $_POST['message'],
                        'username'  => $_POST['username'],
                        'datetime'  => $_POST['datetime'],
                        'trophies'  => [],
                        'upvotes'   => 0
                    ];

                    $post_storage->update($requested_post['id'], $requested_post);           //3. Végül az új állapottal frissíted a rekordot. Ez az id mező a json-ben az objektumhoz rendelt azonosító.
                }
            } else {
                $errors['invalid_comment'] = "Érvénytelen komment! Próbáld újra.";
            }
        }
    }
}