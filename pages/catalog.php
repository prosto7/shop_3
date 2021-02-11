<h2 class="mb-5 ml-2">Catalog page</h2>
<!-- приветствие при авторизации -->
<?php 
    include_once ('classes.php');
    if(isset($_SESSION['register'])) 
    {
    echo "<h3 class='ml-2'>";
    $login = $_SESSION['register'];
    echo "Здравствуйте , " . $login;
    echo "</h3>";
    }   
?>
<!--конец приветствия при авторизации -->

<hr class="bg-info">
<?php

//проверка сессии на логин
    if(isset($_SESSION['register']))  
        {
        // echo "<li id='go' class='nav-item'><a href='index.php?page=exit' class='nav-link text-dark h4'>EXIT</a></li>";
        echo "<script>$('#log_name').addClass('d-none')</script>";
        }
    else {
            echo "<script>$('#log_name').removeClass('d-none')</script>";
        }
    // конец проверки сессии на логин 

    echo '<div id="result" class="row">';

    $items = Item::getItems();

    foreach ($items as $item) {
        $item->drawItem();
    }
    echo '</div>';

?>

<script>
    // добавление в корзину
    function createCookie(ruser, id) 
    {
        $.cookie(ruser, id, {
            expires: 2,
            path: '/'
        });
    }
// конец

$('.lead'   ).liTextLength({
    length: 80,									
    afterLength: '...',									
    fullText:false
});

</script>