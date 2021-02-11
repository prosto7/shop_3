
<ul class="nav nav-pills w-50" id="links_menu">

    <li class="nav-item"><a href="index.php?page=1" class="nav-link text-dark h4">Catalog</a></li>
    <li class="nav-item"><a href="index.php?page=2" class="nav-link text-dark h4">Cart</a></li>
    <li class="nav-item"><a href="index.php?page=3" class="nav-link text-dark h4">Registration</a></li>
    <li class="nav-item"><a href="index.php?page=4" class="nav-link text-dark h4">Admin Forms</a></li>
    <li id='log_name' class='nav-item' data-toggle='modal' data-target='#exampleModal'><a href='#' class='nav-link text-dark h4'>Login</a></li>
    
   
   
<?php
    // если сессия открыта видно кнопку выхода
     if(isset($_SESSION['register']))  {
        echo "<li id='go' class='nav-item'><a href='index.php?page=exit' class='nav-link text-dark h4'>EXIT</a></li>";
       }
    // обнуляем сессию при нажатии exit
    if (isset($_GET['page'])) {
       $est = $_GET['page']; 
     
    //    $out = $_GET['name'];  
       if ($est == 'exit') {
           session_destroy();
       }
    } 
    else 
    if (isset($_GET['name'])) {
        $est = $_GET['name']; 
      
     //    $out = $_GET['name'];  
        if ($est == 'exit') {
            session_destroy();
        }
     } 
    // конец разлогинивания 
?>

</ul>


<?php 


//  запуск функции авторизации
    if (isset($_POST['do_login']) AND $_POST['password'] !== '' AND $_POST['login'] !== '') 
    {
        $users = Tools::checkUser($_POST['login'],$_POST['password']);
    }
    
?>
 <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
  <form action="index.php?page=1" method="post">
    <div class="modal-content"> 
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sign in</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"> 
      <input name="login" class="modal_login" type="text" value="" placeholder = "login">
      <input name="password" type="password" value="" placeholder = "password">
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" name="do_login" class="btn btn-primary">Enter</button>
    
    </div>
      </form>
        

    </div>
  </div>
</div>
