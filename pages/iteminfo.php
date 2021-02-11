
 
<?php 
        include_once("menu.php");
        include_once("classes.php");                        
        include_once('connect.php');
    // <!--конец приветствия при авторизации -->
    
    if (isset($_GET['name'])) {
    $itemid = $_GET['name']; 
   
    
    // $page_item = $_GET['name'."$id"];
    $main_items = Item::fromDb($itemid);
    $main_items->drawItemInfo();
    $some_imgs = draw_it();
   

        // $iname = $main_items[1];
        // $icat = $main_items[2];
        // echo $iname;
    }

    class Item_num {


       public $itemid;
       
       

       function get_num_id()
       {
        $itemid = $_GET['name']; 
        echo " $this->itemid";
       
       
        }
    }
    
    $item_num_id = new Item_num;
    $item_num_id->get_num_id();
    var_dump( $main_items);

    // var_dump ($itemid);
    // var_dump ($some_imgs);
    var_dump ($_GET);
    // $bj = new Item (1);
    // $bj->drawItem(1);

    
?>


<hr class="bg-info">
<?php

//проверка сессии на логин
if(isset($_SESSION['register']))  {
    // echo "<li id='go' class='nav-item'><a href='index.php?page=exit' class='nav-link text-dark h4'>EXIT</a></li>";
    echo "<script>$('#log_name').addClass('d-none')</script>";
    }
       else {
        echo "<script>$('#log_name').removeClass('d-none')</script>";
    }
// конец проверки сессии на логин   
       
    echo '<div id="result" class="row">';

// $items = Item::getItems();
// foreach ($items as $item) {
//     $item->drawItem();
// }
echo '</div>';
?>

<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    
<script>
    $(document).ready(function(){
     
$('.responsive').slick({
  dots: true,
  infinite: false,
  speed: 300,
  slidesToShow: 4,
  slidesToScroll: 4,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
});
    });
</script>




