<?php
session_start();
include "inc/functions/connection.php";
if(!isset($_SESSION["id_user"])){
  header("location:index.php");
}
$id_user = $_SESSION["id_user"];
$conn = connect();
$sql = "SELECT * FROM users WHERE id_user = $id_user";
$query = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($query);
mysqli_close($conn);
$conn = connect();
$sql = "SELECT COUNT(*) as qnt FROM notifications WHERE user = $id_user";
if($q = mysqli_query($conn, $sql)){
  $notifications = mysqli_fetch_assoc($q)["qnt"];
}else{
  $notifications = 0;
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="PT-BR">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Carlos Magno Nascimento">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>BOND</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/pattern.css">
  </head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top" id="navbar-menu">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">BOND</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
      <li>
      <?php
        if($notifications>0){
          echo '<a href="notifications.php" class="btn btn-warning" id="dark-text-nav">'.$notifications
          .' <span class="glyphicon glyphicon-globe"></span></a>';
        }else{
          echo '<a href="notifications.php" class="btn btn-default" id="btn-mobile-nav2"><span class="glyphicon glyphicon-globe"></span></a>';
        }
        ?>
        </li><li>
    <form class="navbar-form navbar-right" onsubmit="return false">
      <div class="form-group">
        <input type="text" id="nav-search-input" class="form-control" placeholder="Pesquisar" value="<?php if(isset($_GET["q"])){echo $_GET["q"];} ?>">
      </div>
      <a id="nav-search-submit" class="btn btn-default"><label class="glyphicon glyphicon-search"></label></a>
    </form>
      </li>
        <li><a href="home.php">Início</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" 
          role="button" aria-haspopup="true" aria-expanded="false">Mais <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="see_all_communities.php">Ver Comunidades</a></li>
            <li><a href="create_community.php">Nova Comunidade</a></li>
            <li><a href="profile.php">Meu Perfil</a></li>
            <li><a href="#">Seguidores</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Configurações</a></li>
            <li><a href="logout.php">Sair</a></li>
          </ul>
        </li>

      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<hr><hr><hr>
<div class="container">
<div class="row panel-gray">
    <div class="col-lg-10 col-lg-offset-1">
      <h3 class="text-center">Notificações <a title="Limpar Notificações" href='del_all_notifications.php' class='btn btn-danger'>
                  <span class='glyphicon glyphicon-remove'></span></a></h3>
        <?php
        $conn = connect();
        $sql = "SELECT *, date_format(notifications.r_date, '%d, %b, %Y, %T') as data_f FROM notifications
        JOIN users ON acting_user = users.id_user
        WHERE user = '$id_user'";
        if($query = mysqli_query($conn, $sql)){
            $notifications = array();
            while($row = mysqli_fetch_assoc($query)){
                $notifications[] = $row;
            }
            function organizer($a, $b){
                $a = $a['data_f'];
                $b = $b['data_f'];

                if ($a == $b) return 0;
                return ($a > $b) ? -1 : 1;
            }
            usort($notifications, "organizer");
            foreach($notifications as $notification){
                $acting_user = $notification["acting_user"];
                $acting_user_fname = $notification["first_name"];
                $acting_user_uname = $notification["username"];
                $post = $notification["post"];
                $community = $notification["community"];
                $type = $notification["type"];
                $id_notification = $notification["id_notification"];
                $time = $notification["data_f"];

                switch($type){
                  case "like":
                    $text = "<strong>$acting_user_fname</strong> (@$acting_user_uname)
                     curtiu sua <a href='see_post.php?p=$post' href='dark-text-link'>publicação</a>";
                    break;
                  case "comment":
                    $text = "<strong>$acting_user_fname</strong> (@$acting_user_uname)
                    comentou sua <a href='see_post.php?p=$post' href='dark-text-link'>publicação</a>";
                    break;
                  default:
                    $text = '';
                }

                echo "<div class='panel panel-primary'>
                <div class='panel-heading'><div class='row'>
                <div class='col-lg-1 col-lg-offset-11'>";
                echo "<a href='del_notification.php?n=$id_notification' class='btn btn-danger'>
                  <span class='glyphicon glyphicon-remove'></span></a>";
                echo "</div></div></div>
                <div class='panel-body'>
                 $text
                </div><div class='panel-footer'>$time</div></div>";
            }
            if(count($notifications)==0){
                echo "<h4 class='text-center'>Você ainda não possui nenhuma notificação...</h4>";
            }
        }
        mysqli_close($conn);
        ?>
    </div>
</div>
</div>



    <script src="js/pattern.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>