<h2 class = "mb-5 ml-2">Cart</h2>

<hr class = "bg-info" >

<?php
   
    $total = 0;
//проверка сессии на логин
    if (isset($_SESSION['register'])) 
    {
        echo "<script>$('#log_name').addClass('d-none')</script>";
    } 
    else 
    {
        echo "<script>$('#log_name').removeClass('d-none')</script>";
    }
    // конец проверки сессии на логин          
    foreach($_COOKIE as $k => $v) 
    {
        if (substr($k, 0, strpos($k, "_"))) 
        {
            $id = substr($k, strpos($k, "_") + 1);
            $item = Item::fromDb($id);
            // $total = Customer::addTotal();  // определили переменную тотал
            $total += $item -> pricesale;
            $item -> drawItemAtCart();
        }

    }
  
    echo '<form class="cart_goods" action="index.php?page=2" method ="post">';
    echo '<hr>';
    echo "<span class='ml-5 text-primary'>Total price: $total</span>";
    echo "<button id='order' class='btn btn-primary btn-lg ml-5 ' name='suborder'>Purchase order</button>";
    echo '</form>';

// обработчки для оформления заказа

    if (isset($_POST['suborder'])) 
    {
    // функция занесения общей суммы заказа пользователя
        Customer::addTotal();
        $id_result = [];
        foreach($_COOKIE as $k => $v) 
        {
            if (substr($k, 0, strpos($k, "_")) === 'cart') 
            {
                $id = substr($k, strpos($k, "_") + 1);
                $item = Item::fromDb($id);
                array_push($id_result, $item -> sale());
                
            }
        }
    
    echo "<h3 class='mt-5 text-success'>Заказ на сумму $total $ выполнен</h3>";
?>
<!-- функция очистки корзины удалить куки -->
<script>
    function deCookie(cookie) 
    {
        var cookies = Cookies.get();
        for (var cookie in cookies) 
        {
            if (cookie.includes('cart'))
            {
                Cookies.remove(cookie,  { path: "/" });
            }  
        }  
    }

    deCookie();

    function hideCart(){
        $('.cart_goods').addClass('d-none');}
    hideCart();
</script>
<!-- конец функция очистки корзины скрыть строки -->

<?php
    } 
?>

<script>
    function eraseCookie(ruser) 
    {
        $.removeCookie(ruser, { path: '/' });
        window.location.reload();
    } 

    $('.cart_item_info').liTextLength({
    length:50,									
    afterLength: '...',									
    fullText:false
});
</script>

