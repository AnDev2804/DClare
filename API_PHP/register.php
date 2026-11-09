<?php
    include_once('connect.php');
    if(($_SERVER["REQUEST_METHOD"]=="POST"))
    {
        if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["pass"]))
        {
            $obj=new conexion();
            $name=$_POST["name"];
            $email=$_POST["email"];
            $pass=$_POST["pass"];
            $res=$obj->register($name,$email,$pass);
            if($res)
            {
                echo "true";
            }
            else
            {
                echo "false";
            }
        }
    }
    else
    {
        echo "ERROR 404 NO DATA RECOVERED";
    }