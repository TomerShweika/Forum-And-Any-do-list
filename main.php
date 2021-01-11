<?php
    require_once "permissions.php";
    
    $MySQLdb = new PDO("mysql:host=127.0.0.1;dbname=forum", "USERNAME", "PASSWORD");//change here
    $MySQLdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $cursor = $MySQLdb->prepare("SELECT COUNT(post_id) FROM posts;");
    $cursor->execute();
    $row = $cursor->fetch();
    if(intval($row["COUNT(post_id)"])>20){
        $cursor1 = $MySQLdb->prepare("TRUNCATE TABLE `posts`");
        $cursor1->execute();
    }
    #$cursor = $MySQLdb->prepare("INSERT INTO users (username, password) value (:username,:password)");
    #$cursor->execute(array(":password"=>$password, ":username"=>$username));
    
    
    $modal="";
    $user     = $_SESSION["user"];
    $userid   = $_SESSION["userid"];
    if (!isset($_SESSION['views'])) { 
        $_SESSION['views'] = 0;
    }
    
    $_SESSION['views'] = $_SESSION['views']+1;
    if($_SESSION['views'] == 1){
        $modal = "$('#myModal').modal('show');";
        $cursor2 = $MySQLdb->prepare("INSERT INTO users_online (user_on) value (:user_on);");
        $cursor2->execute(array(":user_on"=>$_SESSION["user"]));
    }
    $csrf = bin2hex(random_bytes(72));
    $_SESSION['csrf'] = $csrf;
    //setcookie('csrf' , $csrf , time()+3600 , "/" , "" , 0 , 0);
    $device = "";
    function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    if(isMobile()){
        $device = "Smartphone";
    }
    else {
        $device = "PC";
    }
    $cursor3 = $MySQLdb->prepare("SELECT * FROM `users_online`;");
    $cursor3->execute();
    $a = array(); 
    foreach ($cursor3->fetchAll() as $obj)
    {
    if(!in_array($obj["user_on"],$a)){
        array_push($a,$obj["user_on"]);
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forum</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/custom.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
</head>
<body onload=<?php echo $modal; ?>>
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Forum</h4>
        </div>
        <div class="modal-body">
          <p>Welcome to my forum for learn more click on the Instructions button</p>
          <p>Online Users: <?php foreach($a as $value){
                                        echo $value . ", ";
                                    } ?> </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">FORUM</a>
        </div>
        <ul class="nav navbar-nav" style="float: right">
            <li><a href="#" id="logout">Logout</a></li>
        </ul>
        <ul class="nav navbar-nav" style="float: right">
            <li><a href="#"  onclick='swal({
  title: "ברוך הבא",
  text: "אנחנו שמחים שבחרת בפורום שלנו על מנת לקבל הודעות עליך לרענן את הדף (ניתן על ידי הכפתור למטה),  על מנת לרדת אל הצאט למטה ניתן להשתמש בכפתור העליון.",
  icon: "success",
  button: "אישור",
});' id="logout">Instructions</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row" id="page" hidden>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Account Info</div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <p>
                                <kbd>username:</kbd>
                                <span style="float:right;">
                                    <?php echo htmlentities(strip_tags($user), ENT_QUOTES, "UTF-8"); ?>
                                </span>
                            </p>
                        </li>
                        <li class="list-group-item">
                            <p>
                                <kbd>ip address:</kbd>
                                <span style="float:right;">
                                    <?php echo $_SERVER["REMOTE_ADDR"];?>
                                </span>
                            </p>
                        </li>
                        <li class="list-group-item">
                            <p>
                                <kbd>Device:</kbd>
                                <span style="float:right;">
                                    <?php echo $device;?>
                                </span>
                            </p>
                        </li>
                        <li class="list-group-item">
                            <p>
                                <kbd>Times:</kbd>
                                <span style="float:right;">
                                    <?php echo $_SESSION['views'];?>
                                </span>
                            </p>
                        </li>
                        <li class="list-group-item">
                            <p>
                                <kbd>Users Online:</kbd>
                                <span style="float:right;">
                                    <?php
                                    foreach($a as $value){
                                        echo $value . "<br>";
                                    }
                                    ?>
                                </span>
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Chat History
                </div>
                <div class="panel-body">
                    <ul class="list-group" id="post_history">
                        <li class="speech-bubble-left">
                            <h2>Tomer</h2>
                            <p>this is my text nothing special</p>
                        </li>
                        <li class="speech-bubble-right">
                            <h2>Tomer</h2>
                            <p>this is my text nothing special</p>
                        </li>
                        <li class="speech-bubble-left">
                            <h2>Tomer</h2>
                            <p>
                                this is my text nothing special
                            </p>
                            <input name="post_id" value="10" hidden>
                        </li>
                    </ul>
                </div>
            </div>
            <form>
                <div class="input-group">
                    <input id="msg" type="text" class="form-control" name="msg" placeholder="Write your message here...">
                    <input type="hidden" id="csrf" value="<?php echo $csrf; ?>">
                    <span class="input-group-addon"><button id="send_post">Send</button></span>
                </div>
            </form>
        </div>
    </div>
</div>
<h1><?php echo $_SESSION['views']; ?></h1>
<h1><?php print_r($cursor->fetch()); ?></h1>
<script src="./assets/pages/helper.js"></script>
<script src="./assets/pages/main.js"></script>
<script>
function getposts() {
     $.ajax({
    type: "POST",
    url: "/forum/api.php",
    // The key needs to match your method's input parameter (case-sensitive).
    contentType: "application/json; charset=utf-8",
    data: JSON.stringify({"action":"get_all_posts"}),
    dataType: "json",
    success: function(data){ $("#post_history").html(data.data);},
    error: function(errMsg) {
        alert(errMsg);
    }
});
}
setInterval(getposts, 5000);


     /* function doSomething() {

        $.post("/forum/api.php", {"action": "get_all_posts"}, function (data) {
        if (data.success == "true") {
          $("#post_history").html(data.data);
        }
      });
  
      }
      setInterval(doSomething, 5000);
     // the function doSomething will be fired every 5 second
 */
</script>
</body>
</html>
