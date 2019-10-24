<?php
$host="localhost";
$db="products";
$username="user";
$password="password";

$dsn= "mysql:host=$host;dbname=$db";

// POST METHOD
if ($_POST) {

    try { 
        $conn = new PDO($dsn, $username, $password);
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $qty = $_POST['qty'];
        echo $name . $category . $price . $qty;
        if ($conn){
            $query = $conn->prepare('INSERT INTO products (name, category, price, qty) VALUES (?,?,?,?)');
            $results = $query->execute(array($name, $category, $price, $qty));
            
        }
        
        echo json_encode($results);
        
        
    } catch (PDOException $e){
        echo $e->getMessage();
    }
    die();
} 
if (isset($_GET['product'])){

    // GET METHOD
    try { 
        $conn = new PDO($dsn, $username, $password);
        $results = [];
        if ($conn){
            $query = $conn->prepare('SELECT * FROM products WHERE name LIKE ?');
            $query->execute(array("%" . $_GET['product'] . "%" ?? ''));
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode($results);
    } catch (PDOException $e){
        
        echo $e->getMessage();
    }
    
}