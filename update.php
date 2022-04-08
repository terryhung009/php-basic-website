<?php
//建立資料庫連線
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location:index.php');
    exit;
}

$statement = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);

// echo'<pre>';
// var_dump($product);
// echo '</pre>';
// exit;

$errors = [];

$title = $product['title'];
$description = $product['description'];
$price = $product['price'];

// echo '<pre>';
// var_dump($_FILES);
// echo '</pre>';
// exit;

// ?image=&title=&description=&price=

// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
// exit;

// echo '<pre>';
// var_dump($_SERVER);
// echo '</pre>';
// exit;

// echo $_SERVER['REQUEST_METHOD'] . '<br>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];




    if (!$title) {
        $errors[] = 'Product title is required';
    }
    if (!$price) {
        $errors[] = 'Product price is required';
    }
    if (!is_dir('images')) {
        mkdir('images');
    }

    if (empty($errors)) {
        $image = $_FILES['image'] ?? null;
        $imagePath = $product['image'];
        // echo '<pre>';
        // var_dump($image);
        // echo '</pre>';
        // exit;

        

        if ($image && $image['tmp_name']) {
            if ($product['image']) {
                unlink($product['image']);
            }


            $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
            mkdir(dirname($imagePath));

            // echo '<pre>';
            // var_dump($imagePath);
            // echo '</pre>';
            // exit;
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        // exit;

        $statement = $pdo->prepare(
            "UPDATE products SET title = :title, image = :image,
             description = :description, price = :price WHERE id = :id "
        );
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
        // $statement->bindValue(':date', $date);

        $statement->execute();
        header('Location:index.php');
    }
}

function randomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }

    return $str;
}




?>


<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="app.css">
    <title>編輯 Product</title>
</head>

<body>
    <p>
        <a href="index.php" class="btn btn-secondary">回到Products</a>
    </p>

    <h1>編輯 Product <b><?php echo $product['title'] ?></b> </h1>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div>
                    <?php echo $error ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <?php if ($product['image']) : ?>
            <img src="<?php echo $product['image'] ?>" alt="" class="update-image">

        <?php endif; ?>


        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <input type="file" class="form-control" name="image">

        </div>
        <div class="mb-3">
            <label class="form-label">Product Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo $title ?>">

        </div>
        <div class=" mb-3">
            <label class="form-label">Product Description</label>
            <textarea class="form-control" name="description"><?php echo $description ?></textarea>

        </div>
        <div class="mb-3">
            <label class="form-label">Product Price</label>
            <input type="number" step=".01" class="form-control" name="price" value="<?php echo $price ?>">

        </div>


        <button type="submit" class="btn btn-primary">Submit</button>
    </form>











    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.1/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>
    -->
</body>

</html>