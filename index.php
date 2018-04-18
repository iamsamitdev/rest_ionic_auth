<?php
session_start();
require 'config/config.php';

// Method GET
if(@$_SERVER['REQUEST_METHOD'] === 'GET'){
    if(@$_GET['action']=='userlist'){
        echo "Action User List";
    }elseif(@$_GET['action']=='profile'){
        echo "Action Profile List";
    }else{
        echo '{"error":{"text":"Not found action get"}}';
    }

// Method POST
}elseif(@$_SERVER['REQUEST_METHOD'] === 'POST'){
    if(@$_GET['action']=='login'){
        
        $db = getDB();
        $userData = '';

        // JSON
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE); //convert JSON into array

        $username = $input['username'];
        $password = $input['password'];

        if(!empty($username) and !empty($password))
        {
            $sql = "SELECT id, fullname, email, username FROM users WHERE (username=:username or email=:username) and password=:password";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("username", $username, PDO::PARAM_STR);
            $password_hash=hash('sha256',$password);
            $stmt->bindParam("password", $password_hash, PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            $userData = $stmt->fetch(PDO::FETCH_OBJ);

            if($mainCount==1)
            {
                // echo '{"success":{"text":"Login success"}}';
                $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            }else{
                echo '{"Fail":{"text":"Bad request wrong username and password"}}';
            }
        }else{
            echo '{"Fail":{"text":"Invalid value !!!"}}';
        }

    }elseif(@$_GET['action']=='signup'){

        $db = getDB();
        $userData = '';

        // JSON
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE); //convert JSON into array

        $username = $input['username'];
        $password = $input['password'];
        $fullname = $input['fullname'];
        $email = $input['email'];
        $tel = $input['tel'];
        $user_type = $input['user_type'];
        $status = 1;

        if(!empty($username) and !empty($password) and !empty($fullname) and !empty($tel))
        {
            // Check if exsist username and email
            $sql_chk = "SELECT id FROM users WHERE username=:username or email=:email";
            $stmt_chk = $db->prepare($sql_chk);
            $stmt_chk->bindParam("username", $username,PDO::PARAM_STR);
            $stmt_chk->bindParam("email", $email,PDO::PARAM_STR);
            $stmt_chk->execute();
            $mainCount=$stmt_chk->rowCount();

            if($mainCount==0)
            {
                $sql="INSERT INTO users(username,password,fullname,email,tel,user_type,status) VALUES(:username,:password,:fullname,:email,:tel,:user_type,:status)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("username", $username,PDO::PARAM_STR);
                $password_hash =hash('sha256',$password);
                $stmt->bindParam("password", $password_hash,PDO::PARAM_STR);
                $stmt->bindParam("fullname", $fullname,PDO::PARAM_STR);
                $stmt->bindParam("email", $email,PDO::PARAM_STR);
                $stmt->bindParam("tel", $tel,PDO::PARAM_STR);
                $stmt->bindParam("user_type", $user_type,PDO::PARAM_STR);
                $stmt->bindParam("status", $status,PDO::PARAM_STR);

                if($stmt->execute()){

                    //echo '{"Success":{"text":"Add new record success"}}';
                    $input_data = array(
                        "username" => $username,
                        "fullname" => $fullname,
                        "email" => $email,
                        "tel" => $tel,
                        "user_type" => $user_type
                    );

                    $userData = json_encode($input_data);
                    echo '{"userData": ' .$userData . '}';
                }else{
                    echo '{"Fail":{"text":"Add new record fail !!!"}}';
                }
            }else{
                echo '{"fail":{"text":"Already username or email try again !!!"}}';
            }
        }else{
            echo '{"Fail":{"text":"Invalid value !!!"}}';
        }

    }else{
        echo '{"error":{"text":"Not found action post"}}';
    }

// Not found any method
}else{
    http_response_code(400);
    echo '{"error":{"text":"Bad Request method"}}';
}





