<?php
    require_once "./permissions.php";
    require_once "./CommonInterface.php";
    require_once "./DatabaseInterface.php";

    $userid = $_SESSION["userid"];
    $username = $_SESSION["user"];
    $databaseObj = new DatabaseInterface();

    $MySQLdb = new PDO("mysql:host=127.0.0.1;dbname=forum", "root", "");
    $MySQLdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



    $input = json_decode(file_get_contents('php://input'),false);

    if(!is_object($input))
    {
        return_error("nice try :)");
    }

    if(!isset($input->action))
    {
        return_error("nice try :)");
    }

    switch ($input->action)
    {
        case "get_all_posts":
            if ($data = $databaseObj->GetAllPosts($userid))
			{
				return_success($data);
			}
			else
			{
				return_error("Malformed request");
			}			
            break;

        case "new_post":

            if ($_SESSION['csrf'] != $input->csrf){
                return_error("Malformed request");
            }

            if ($databaseObj->NewPost($userid, $input->data, $username))
            {
                return_success($input->data);
            }
            else
            {
                return_error("Malformed request");
            }
            break;

        case "edit_post":
            if ($databaseObj->EditPost($input->post_id, $input->data))
            {
                return_success($input->data);
            }
            else
            {
                return_error("Malformed request");
            }
            break;

        case "logout":
            $cursor2 = $MySQLdb->prepare("DELETE FROM users_online WHERE user_on=:user_on");
            $cursor2->execute(array(":user_on"=>$username));
            session_destroy();
            return_success("logged out");
            break;

        default:
            return_error("Malformed request");
    }

