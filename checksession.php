<?php

if (session_status() === PHP_SESSION_NONE){
    session_start();


function checkUser(){
    $_SESSION['URI'] = '';
    if($_SESSION['loggedin'] ==1)

    
    return TRUE;

    else{
        $_SESSION['URI'] ='/' .$_SERVER['REQUEST_URI'];
        header(('Location:/bnb/login.php'),true, 303);

    }
}

function loginStatus(){

    $un = $_SESSION['username'];

    if($_SESSION['loggedin'] == 1){
        echo "<h6>Logged in as $un</h6>";
    }
    else{
        echo "<h5>Logged Out</h5>";
    }
}

function login($id, $username){

    if($_SESSION['loggedin'] == 0 and !empty($_SESSION['URI'])){
        $uri = $_SESSION['URI'];

    }
    else{
        $_SESSION['URI'] = '/bnb/index.php';
        $uri = $_SESSION['URI'];
    }

    header('Location: /bnb/index.php', true, 303);

    $_SESSION['loggedin']=1;
    $_SESSION['userid']=$id;
    $_SESSION['username']=$username;
    $_SESSION['URI']='';
}

function logout(){
    $_SESSION['loggedin']= 0;
    $_SESSION['userid']= -1;
    $_SESSION['username']= '';
    $_SESSION['URI']='';
    header('Location:/bnb/login.php', true, 303);

}

}

?>