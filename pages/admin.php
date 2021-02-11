<h2 class="mb-5 ml-2">Admin Forms</h2>
<hr class="bg-info">

<?php
include_once('connect.php');
// проверка сессии 
if(isset($_SESSION['register']))  
    {
    echo "<script>$('#log_name').addClass('d-none')</script>";
    }
    else 
    {
    echo "<script>$('#log_name').removeClass('d-none')</script>";
    }
       
// конец проверки сессии на логин   



    if (!isset($_POST['addbtn'])) {

?>

<form action="index.php?page=4" method="post" enctype="multipart/form-data">
  <!-- тут должна быть селект для выбора категории товара -->
<div class="form-group">
<label for="category">
<select name="catid" id="category">

<?php

    $pdo = Tools::connect();
    $ps = $pdo->query("SELECT * FROM categories");
    while ($row = $ps->fetch()) {
      echo "<option value='{$row["id"]}'>{$row['category']}</option>";
    }

    ?>

  </select>
  <input type="text" name="cat_name" id="cat_name" placeholder="Категория">
  <input type="submit" name="add_cat" id="add_cat" value="Добавить" class="btn btn-sm btn-primary">
  <input type="submit" name="del_cat" value="Удалить" value="Удалить" class="btn btn-sm btn-danger">
  </label>

  <?php

  // добавление категории 

    if (isset($_POST['add_cat'])) {
            try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("INSERT INTO categories(category) VALUES (:cat_name)");
            $ps->bindParam(':cat_name', $cat_name);
            $cat_name = $_POST['cat_name'];
            $ps->execute();
            }
              catch (PDOException $e) {
                echo $e->getMessage();
                return false;
          }
          echo '<script>window.location=document.URL</script>';
        }
  
        // удаление категории бета в
      //   if (isset($_POST['del_cat'])) { 
      //    try {
      //     $catid=  $_POST['catid'];
      //     $pdo = Tools::connect();
      //     $del= "DELETE FROM categories WHERE id =:catid"; 
      //     $stmt =$pdo->prepare($del);
      //     $stmt->bindParam(':catid',$catid);
      //     $stmt->execute();
      //     }
      //       catch (PDOException $e) {
      //         echo $e->getMessage();
      //         // return false;
      //   }
      //   // echo '<script>window.location=document.URL</script>';
      // }

?>

</div>
  <div class="form-group">

    <label for="name">Name:
      <input type="text" name="name" id="name">
    </label>
  </div>

  <div class="form-group">
    <p>Входящая цена и цена продажи</p>
    <label for="pricein">Pricein:
      <input type="number" name="pricein" id="pricein">
    </label>
    <label for="pricesale">Pricesale:
      <input type="number" name="pricesale" id="pricesale">
    </label>
    <div class="form-group">
      <label for="info">Info:
        <textarea name="info" id="info" cols="50" rows="5"></textarea>
      </label>
    </div>
    <div class="form-group">
      <label for="imagepath">Image:
        <input type="file" accept="image/*" name="imagepath"  id="imagepath">
      </label>
    </div>
  </div>

<input type="submit" class="btn btn-primary" name="addbtn" value="Add good">

</form>

<?php 

} else {
  if(is_uploaded_file($_FILES['imagepath']['tmp_name'])) {
    $path = "images/goods/".$_FILES['imagepath']['name'];
    move_uploaded_file($_FILES['imagepath']['tmp_name'], $path);
  }
  $item = new Item(trim($_POST['name']),$_POST['catid'], $_POST['pricein'], $_POST['pricesale'],$_POST['info'], $path);
  $item->intoDb();
}

?>
<!-- добавление картинок -->
<div class="col-6">
        <?php
        $link = connect();
        echo '<form action="index.php?page=4" method="post" enctype="multipart/form-data" class="input-group mt-5">';
        $sel = "SELECT ca.id, ca.category , it.id , it.itemname, it.catid FROM categories ca, items it WHERE ca.id= it.catid ";
        $res = mysqli_query($link, $sel);
        echo '<select id="item_id" name="item_id">';
        $to = 0;
        while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {

            echo "<option value='$row[2]'>$row[1] | $row[2] | $row[3]</option>";
           
        }
     
        echo '</select>';
        // mysqli_free_result($res); // очищает выборку в памяти
        
        echo '<input type="file" name="file[]" multiple accept="image/*">';
        echo '<input type="submit" id="elem" name="addimage" value="Добавить" class="btn btn-sm btn-info">';
        echo '</form>';
        if (isset($_POST['addimage'])) {
            $itemid = $_POST['item_id'];
            foreach ($_FILES['file']['name'] as $k => $v) {
                if ($_FILES['file']['error'][$k] !== 0) {
                    echo '<script>alert("Upload file error" ' . $v . ')</script>';
                    continue;
                }
                if (move_uploaded_file($_FILES['file']['tmp_name'][$k], 'images/' . $v)) {
                 $ins = "INSERT INTO images(imagepath,itemid) VALUES ('images/$v','$itemid')";
               
                    mysqli_query($link, $ins);
                }
            }
        }

    
        ?>
            <!-- <script> document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("elem").onclick = function() {
              alert(document.getElementById("item_id").value);
            
            };
          });</script> -->
<!-- <?php
if (isset($_GET['u_name']))
        {
            echo "Значение JavaScript-переменной: ". $_GET['u_name'];
        }
        
        else
        {
            echo '<script type="text/javascript">';
            echo 'document.location.href="' . $_SERVER['REQUEST_URI'] . '?u_name=" + new_id';
            echo '</script>';
            exit();
        }
        var_dump ($_SERVER);
        ?> -->
     
    </div>