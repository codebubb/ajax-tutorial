<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

</head>
<body>
<?php
$host="localhost";
$db="products";
$username="user";
$password="password";

$dsn= "mysql:host=$host;dbname=$db";

if ($_POST) {

    try { 
        $conn = new PDO($dsn, $username, $password);
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $qty = $_POST['qty'];
        
        if ($conn){
            $query = $conn->prepare('INSERT INTO products (name, category, price, qty) VALUES (?,?,?,?)');
            $results = $query->execute(array($name, $category, $price, $qty));
            
        }
        
        echo '<div class="alert alert-success">Product added successfully</div>';
        
    } catch (PDOException $e){
        echo $e->getMessage();
    }
}
?>
    <div id="alerts"></div>
    <div class="container">
        <div class="row">
        <div class="col-4">
            <h1>Add Product</h1>
                <form id="addProduct" method="POST" action="index.php">
                    <input class="form-control" id="productName" type="text" name="name" placeholder="Product Name">
                    <input class="form-control" id="productCategory" type="text" name="category" placeholder="Category">
                    <input class="form-control" id="productPrice" type="text" name="price" placeholder="Price">
                    <input class="form-control" id="productQty" type="text" name="qty" placeholder="Quantity">
                    <button class="btn btn-primary btn-block">Add</button>
                </form>
            <h1>Lookup Products</h1>
                <form id="lookupProducts" method="GET" action="index.php">
                    <input class="form-control" id="productLookup" name="product" type="text" placeholder="Lookup Product">
                    <button class="btn btn-primary btn-block">Lookup</button>
                </form>
        </div>
        <div class="col-8" id="results">
        <?php
            if (isset($_GET['product'])) {
                try { 
                    $conn = new PDO($dsn, $username, $password);
                    $results = [];
                    if ($conn){
                        $query = $conn->prepare('SELECT * FROM products WHERE name LIKE ?');
                        $query->execute(array("%" . $_GET['product'] . "%" ?? ''));
                        $results = $query->fetchAll(PDO::FETCH_ASSOC);
                    }
                    echo '<table class="table table-striped"><tr><th>Name</th><th>Category</th><th>Price</th><th>Qty</th>';

                    foreach ($results as $result): ?>
                        <tr>
                            <td><?= $result['name'] ?></td>
                            <td><?= $result['category'] ?></td>
                            <td><?= $result['price'] ?></td>
                            <td><?= $result['qty']?></td>
                        </tr>
                    <?php endforeach;
                    echo '</table>';
                } catch (PDOException $e){
                    echo $e->getMessage();
                }
            }
        ?>
        </div>
    </div>
    <script>
        const productLookupElem = document.getElementById('productLookup');
        const resultsElem = document.getElementById('results');
        productLookupElem.addEventListener('keyup', () => {
            const { value } = productLookupElem;
            fetch(`http://localhost/products.php?product=${value}`)
                .then(response => response.json())
                .then(results => {
                    let resultsHTML = '<table class="table table-striped"><tr><th>Name</th><th>Category</th><th>Price</th><th>Qty</th>';
                    results.forEach(item => {
                        resultsHTML += `<tr>
                            <td>${item.name}</td>
                            <td>${item.category}</td>
                            <td>${item.price}</td>
                            <td>${item.qty}</td>
                        </tr>`;
                    });
                    resultsHTML += '</table>';
                    resultsElem.innerHTML = resultsHTML;
                });
        });

        const addProductForm = document.getElementById('addProduct');
        const alertsElem = document.getElementById('alerts')
        addProductForm.addEventListener('submit', (e) => {
            e.preventDefault();
            fetch('http://localhost/products.php', { 
                method: 'POST',
                body: new FormData(addProductForm),
            })
            .then(() => {
                alertsElem.innerHTML = '<div class="alert alert-success">Product added successfully</div>';
                setTimeout(() => {
                    alertsElem.innerHTML = '';
                }, 4000);
            })
            .catch(console.log);
        });
    </script>
</div>
</body>
</html>
