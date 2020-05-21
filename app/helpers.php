<?php

function gravatr_url($email)
{
    $email = md5($email);

//    return "https://gravatar.com/avatar/{$email}" . http_build_query([
//            's' => 60,
//            'd' => "http://s3.amazonaws.com/laracasts/images/default-square-avatar.jpg"
//        ]);

    return "https://gravatar.com/avatar/{$email}?s=60&d=http://s3.amazonaws.com/laracasts/images/default-square-avatar.jpg";
}

?>
