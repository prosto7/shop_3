<?php
    include_once('connect.php');
    function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // error was suppressed with the @-operator
        if (0 === error_reporting())
        {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    set_error_handler('handleError');

    class Tools 
    {
        static function connect($host="127.0.0.1", $user='root', $pass='', $dbname='shop') 
        {
            // PDO (PHP data object) - механизм взаимодйствия с СУБД(система управления базами данных)
            // PDO - позволяет облегчить рутинные задачи при выполнении запросов и содержит защитные механизмы при работе с СУБД
            // определим DSN(Data source name) - сведения для подключения к БД.
            $cs = "mysql:host=$host;dbname=$dbname;charset=utf8";

            // массив опций для создания PDO
            $options = 
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
            ];

            try 
            {
                $pdo = new PDO($cs, $user, $pass, $options);
                return $pdo;
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }

    // функция проверки зарегистрированного пользователя при авторизации

        static function checkUser($login, $password ) 
        {
            try 
            {
                $pdo = Tools::connect();
                $ps = $pdo->prepare("SELECT COUNT(*) FROM `customers` WHERE `login` = :login AND `pass` = :password;");
                $ps->bindParam(':login', $login);
                $ps->bindParam(':password', $password);       
                $ps->execute();
                $row_count = $ps->fetchColumn();

                if ($row_count == 1) 
                {
                    $result=true;
                    echo "<h5 class='text-success'>Вход выполнен</h5>";
                    $_SESSION['register'] = $login;
                    echo "<script>$('#go').removeClass('d-none')</script>";
                    echo "<script>$('#log_name').addClass('d-none')</script>";
                    return true;
                }
                else 
                {
                    $result=false;
                    echo "<h5 class='text-danger'>Вход невыполнен</h5>";   
                    return false;
                }

            }
            catch(PDOException $e) { die("Secured"); }
            $ps = null;
            $pdo = null;
            return $result;   
        }
    }
    //конец  функции проверки зарегистрированного пользователя при авторизации



    class Customer {
        public $id;
        public $login;
        public $pass;
        public $roleid;
        public $discount;
        public $total;
        public $imagepath;

        function __construct($login, $pass, $imagepath, $id = 0) 
        {
            $this->login = trim($login);
            $this->pass = trim($pass);
            $this->imagepath = $imagepath;
            $this->id = $id;
            $this->total = 0;
            $this->discount = 0;
            $this->roleid = 2;
        }


        // функция занесения общей суммы заказа пользователя
        static function addTotal() 
        {
            try 
            {
                $login = $_SESSION['register'] ;
                $pdo = Tools::connect();
                global $total;  // по другому не получалось
                $ps = $pdo->prepare("UPDATE `customers` SET `total` = `total` + '$total'  WHERE `customers`.`login` = '$login'");
                if ($total > 0) 
                {
                $ps->execute(); 
                }
                else 
                {
                    return false;
                }
            } 
            catch (PDOException $e) 
            {
                echo $e->getMessage();
                return false;
            }
        }
        // конец функции занесения общей суммы заказа пользователя

        function register() {
            if($this->login === '' || $this->pass === '') {
                echo "<h3 class='text-danger'>Заполните все поля</h3>";
                return false;
            }

            if(strlen($this->login) < 3 || strlen($this->login) > 32 || strlen($this->pass) < 3 || strlen($this->pass) > 128 ) {
                echo "<h3 class='text-danger'>Не корректная длина полей</h3>";
                return false;
            }

            $this->intoDb();

            return true;
        }

        function intoDb() { 
            try {
                $pdo = Tools::connect();
                // подготовим(prepare) запрос за добавление пользователя
                $ps = $pdo->prepare("INSERT INTO customers(login, pass, roleid, discount, total, imagepath) VALUES (:login, :pass, :roleid, :discount, :total, :imagepath)");

                // разименовывание объета this, и преобразование к массиву
                $ar = (array)$this; // $ar = [:id, :login, :pass, :roleid, :discount, :total, :imagepath]
                array_shift($ar); // удаляем первый элемент массива, т.е. :id
                // ar = :login, :pass, :roleid, :discount, :total, :imagepath
                $ps->execute($ar);
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    class Item {
        public $id;
        public $itemname;
        public $catid;
        public $pricein;
        public $pricesale;
        public $info;
        public $rate;
        public $imagepath;
        public $action;
        

    function __construct($itemname,$catid, $pricein, $pricesale, $info, $imagepath, $rate=0, 
    $action=0, $id=0 ) {
        $this->id=$id;
        $this->itemname=$itemname;
        $this->catid=$catid;
        $this->pricein=$pricein;
        $this->pricesale=$pricesale;
        $this->info=$info;
        $this->rate=$rate;
        $this->imagepath=$imagepath;
        $this->action=$action;
        }

    function intoDb() {
        try {
            
            $pdo = Tools::connect();
            $ps = $pdo->prepare("INSERT INTO items( itemname,catid, pricein, pricesale, info, imagepath, rate,
            action) VALUES(:itemname,:catid,:pricein,:pricesale,:info,:imagepath,:rate,
            :action)");
            $ar = (array)$this;
            array_shift($ar);
            $ps->execute($ar);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function intoImgDb() {
        try {
            
            $pdo = Tools::connect();
            $ps = $pdo->prepare("INSERT INTO images(itemid,imagepath) VALUES(:itemid,:imagepath)");
            $ar = (array)$this;
            array_shift($ar);
            $ps->execute($ar);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }


    static function fromDb($id) {
    try {
        $pdo = Tools::connect();
        $ps = $pdo->prepare("SELECT * FROM items WHERE id=?");  // ? - 
        $ps->execute([$id]);
        $row = $ps->fetch();
        $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'],$row['info'],$row['imagepath'],$row['rate'],$row['action'] ,$row['id']);
        return $item;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;

    }
    }


    public function deleteProject($catid) {
        if (isset($_POST['del_cat'])) { 
        $catid =  $_POST['catid'];
        $pdo = Tools::connect();
        $del= "DELETE FROM categories WHERE id = :catid"; 
        $stmt =$pdo->prepare($del);
        $stmt->bindParam(':catid',$catid);
        $stmt->execute();
        }
    }
    
    static function getItems($catid = 0) {

        try {

                $pdo = Tools::connect();
                // если категория не выбрана на странице саталог то выбираем все товары

                if($catid === 0) {
                    $ps = $pdo->query("SELECT * FROM items");
                } else {
                    $ps = $pdo-> query("SELECT * FROM items WHERE catid=?");
                    $ps->execute([$catid]);
                }
                while ($row=$ps->fetch()) {
                $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'],$row['info'],$row['imagepath'],$row['rate'],$row['action'] ,$row['id']);
                
                // создадим массив экземпляров класса item
                $items[] = $item;
            
            }
                
                return  $items ;
        
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    } 


    // карточки товара


    


    function drawItem()
    {
       
        echo '<div class=" d-flex flex-column bd-highlight mb-3 col-sm border mr-3 new_goods rounded">';
        // шапка товара 

        if (isset($_POST["$this->id"])) {
            echo "<h3 class='text-success'>Добавлено в корзину</h3>";
        
        }
        echo '<div class=" p-2 bd-highlight mt-1 bg-light">';
        echo "<a href='index.php?name={$this->id}' target='_blank' class='ml-2 float-left'>$this->itemname</a>";
        echo  "<span class='mr-2 float-right'>$this->rate</span>";
        echo '</div>';
    
           // изображение товара
        echo "<div class=' d-flex justify-content-center p-2 bd-highlight item-catalog__img mt-5 '>";
        echo "<img src='{$this->imagepath}' alt='image' class='img-fluid rounded'>";
        echo '</div>';

        //описание товара
        echo "<div class='p-2 bd-highlight mt-1 text-center item-catalog__info'>";
        echo "<span class ='lead'>$this->info</span>";
        echo '</div>';

        // кнопка добавления в корзину
        echo '<div class="p-2 mt-1 add_to_cart align-self-center mt-auto container-fluid">';
        $ruser ='';
        $ruser = 'cart_'.$this->id; 
        echo "<form method='post'><input type='submit' value='Add to cart' name='$this->id' class='btn btn-info btn-lg btn-block' onclick=createCookie('".$ruser."','".$this->id."')></input></form>";
        echo '</div>';
        echo '</div>';
    
    }
    // $link = connect();
    function drawItemInfo() {
       echo '<div class="container-fluid item_info">';
       echo '<div class="name_item row p-2 bd-highlight mt-1 bg-light ">';
       echo "<h3>$this->itemname</h3>";
       echo '</div>';
       echo "<div class='d-flex justify-content-center p-2 bd-highlight item-card__img mt-5 row'>";
       echo "<img src='{$this->imagepath}' alt='image' class='img-fluid'>";
       echo '</div>';
       echo  "<div class='item_article container'><p>$this->info</p></div>";
       echo '</div>';
       

}
    


    function drawItemAtCart() {
        echo '<div class="cart_goods">';
        echo '<hr>';
        echo '<div class="row m-2 cart_item_img">';
        echo "<img src='{$this->imagepath}' alt='image' class='col-1 img-fluid'>";
        echo "<span class='h4 col-3 m-5 align-self-center cart_item_info'>$this->itemname $this->info</span>";
        echo '<div class="vl"></div>';
        echo "<span class='h4 col-3 m-5 align-self-center'>Price:  $this->pricesale</span>";
        echo '<div class="vl"></div>';
        $ruser = 'cart_'.$this->id; 
        echo "<button class='btn btn-danger del_click m-5 align-self-center' onclick=eraseCookie('".$ruser."')>Delete (X)</button>";
        echo '</div>';
        echo '</div>';
    }

    function sale() {
        try {
        $pdo = Tools::connect();
        $login = $_SESSION['register'];   
        $ins= "INSERT INTO sales(customername,itemname, pricein,pricesale,datesale) VALUES (?,?,?,?,?)";
        $ps=$pdo->prepare($ins);
        $ps->execute([$login, $this->itemname, $this->pricein, $this->pricesale, @date("Y/m/d H:i:s")]);  
        return $this->id;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    static function SMTP ($id_result) {
        require_once ("PHPMailer/PHPMailerAutoload.php") ;
        require_once ("private/private_data.php");
        $mail= new PHPMailer; 
        $mail->CharSet ="UTF-8";
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.mail.ru';
        $mail->Port = 25;
        $mail->Username=MAIL;
        $mail->Password=PASS;
        $mail->setFrom('roman1199@mail.ru', 'Shop');
        $mail->addAddress('roman1199@mail.ru', 'From site');
        $mail->Subject  = 'Новый заказ на сайте Shop';
        $body = "<table cellspacing='0' cellpadding='0' border='2' width='800' style='background-color: green !important;'>";
        $arr_items = [];
        $i = 0;
        foreach ($id_result as $id) {
            $item = self::fromDb($id);
            array_push($arr_items,$item->itemname, $item->pricesale, $item->info); // для csv файла
            $mail->addEmbeddedImage($item->imagepath,'item'.++$i);
            $body .= "
            <tr>
            <th>$item->itemname</th>
            <td>$item->pricesale</td>
            <td>$item->info</td>
            <td><img src='cid:item{$i}' alt='item' height='100'></td>
            </tr>";

        }

        $body .= '</table>';
        $mail->msgHTML($body);
        try {
            $mail->send(); 
        } catch (phpmailerException $e) {
            echo $e->getMessage();
            
        }
        try {
        $csv = new CSV('private/excel_file.csv');
        $csv->setCSV($arr_items);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
    }
//     class Img 
// {
//         public $id;
//         public $itemid;
//         public $imagepath;

//      public function __construct($itemid, $imagepath ) {
//             $this->itemid=$itemid;
//             $this->imagepath=$imagepath;
//         }
//     static function imgFromDb($itemid) 
//     {
//         $itemid = $_GET['name'];
//         try {
//             $pdo = Tools::connect();
//             $stmt = $pdo->prepare("SELECT imagepath FROM images WHERE `itemid` = $itemid"); 
//             $stmt->execute();
           
//             while ($row=$stmt->fetch()) 
//                 {
//                     echo "<img class='carousel-image' alt='Image Caption' src='./$row[0]' >";
//                 }
//                 $itemid = new Img($row['itemid'],$row['imagepath']);
//                 return $itemid;
           
//         } catch (PDOException $e) {
//             echo $e->getMessage();
//             return false;
    
//         }
        
//     }
   
     
// }

function draw_it () {



    $link = connect();


    if (isset($_GET['name'])) {
        $item_id = $_GET['name']; 
        $res = mysqli_query($link, "SELECT imagepath FROM images WHERE itemid = $item_id");
        echo '<div class="carousel-container responsive">';
        while ($row = mysqli_fetch_array($res, MYSQLI_NUM)){
        echo "<img class='carousel-image' alt='Image Caption' src='./$row[0]' >";
    }
    echo '</div>';
    }

    class CSV {
        private $csv_file = null;
        public function __construct ($csv_file) 
    {
    $this->csv_file=$csv_file;
    }

    function setCSV($arr_item) {
        $arr_item = [];
        $file = fopen($this->csv_file, 'a+');
        foreach ($arr_item as $item) {
            fputcsv($file,[$item], ';');
        }
        fclose($file);
    }
    }
}

    // class Path_Img {

    //     public $id_img;
    //     public $imagepath;

    //     function get_path () {
    //         $pdo = Tools::connect();
    //         $sql = 'SELECT name FROM users WHERE email = ?';
            


    //     }


    // }