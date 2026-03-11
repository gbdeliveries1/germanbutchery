<?php
include "../../on/on.php";
session_start();

// Check admin login
if(!isset($_SESSION['GBDELIVERING_ADMIN_USER_2021']) || empty($_SESSION['GBDELIVERING_ADMIN_USER_2021'])) {
    header("location:index.php");
    exit;
}

$action = $_POST['action'] ?? '';

// ADD NEW PRODUCT
if ($action === 'add') {
    $name = trim($_POST['product_name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $price = (int) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $unit = trim($_POST['unit'] ?? 'pcs');
    if (empty($unit)) $unit = 'pcs';
    $minimum_order = (int) ($_POST['minimum_order'] ?? 1);
    if ($minimum_order < 1) $minimum_order = 1;
    
    if ($name && $category_id > 0) {
        // Insert product with unit and minimum_order
        $stmt = $conn->prepare("INSERT INTO product (product_name, category_id, unit, minimum_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $name, $category_id, $unit, $minimum_order);
        $stmt->execute();
        $product_id = $conn->insert_id;
        $stmt->close();
        
        if ($product_id > 0) {
            // Insert price
            $stmt = $conn->prepare("INSERT INTO product_price (product_id, price) VALUES (?, ?)");
            $stmt->bind_param("ii", $product_id, $price);
            $stmt->execute();
            $stmt->close();
            
            // Insert stock
            $stmt = $conn->prepare("INSERT INTO product_stock (product_id, stock_quantity) VALUES (?, ?)");
            $stmt->bind_param("ii", $product_id, $stock);
            $stmt->execute();
            $stmt->close();
            
            // Handle image upload
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../uploads/';
                $ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                $fileName = 'prod_' . $product_id . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                    $stmt = $conn->prepare("INSERT INTO product_picture (product_id, picture) VALUES (?, ?)");
                    $stmt->bind_param("is", $product_id, $fileName);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
    
    header("Location: index.php?page=bulk_editor&success=added");
    exit;
}

// UPDATE PRODUCT FIELD
if ($action === 'update') {
    $pid = (int) ($_POST['pid'] ?? 0);
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';
    
    if ($pid > 0 && $field) {
        switch ($field) {
            case 'name':
                $stmt = $conn->prepare("UPDATE product SET product_name = ? WHERE product_id = ?");
                $stmt->bind_param("si", $value, $pid);
                $stmt->execute();
                $stmt->close();
                break;
                
            case 'category':
                $catId = (int) $value;
                $stmt = $conn->prepare("UPDATE product SET category_id = ? WHERE product_id = ?");
                $stmt->bind_param("ii", $catId, $pid);
                $stmt->execute();
                $stmt->close();
                break;
                
            case 'price':
                $priceVal = (int) $value;
                $check = $conn->prepare("SELECT product_id FROM product_price WHERE product_id = ?");
                $check->bind_param("i", $pid);
                $check->execute();
                $exists = $check->get_result()->num_rows > 0;
                $check->close();
                
                if ($exists) {
                    $stmt = $conn->prepare("UPDATE product_price SET price = ? WHERE product_id = ?");
                } else {
                    $stmt = $conn->prepare("INSERT INTO product_price (price, product_id) VALUES (?, ?)");
                }
                $stmt->bind_param("ii", $priceVal, $pid);
                $stmt->execute();
                $stmt->close();
                break;
                
            case 'stock':
                $stockVal = (int) $value;
                $check = $conn->prepare("SELECT product_id FROM product_stock WHERE product_id = ?");
                $check->bind_param("i", $pid);
                $check->execute();
                $exists = $check->get_result()->num_rows > 0;
                $check->close();
                
                if ($exists) {
                    $stmt = $conn->prepare("UPDATE product_stock SET stock_quantity = ? WHERE product_id = ?");
                } else {
                    $stmt = $conn->prepare("INSERT INTO product_stock (stock_quantity, product_id) VALUES (?, ?)");
                }
                $stmt->bind_param("ii", $stockVal, $pid);
                $stmt->execute();
                $stmt->close();
                break;
                
            case 'unit':
                $unitVal = trim($value);
                if (empty($unitVal)) $unitVal = 'pcs';
                $stmt = $conn->prepare("UPDATE product SET unit = ? WHERE product_id = ?");
                $stmt->bind_param("si", $unitVal, $pid);
                $stmt->execute();
                $stmt->close();
                break;
                
            case 'minimum_order':
                $minVal = (int) $value;
                if ($minVal < 1) $minVal = 1;
                $stmt = $conn->prepare("UPDATE product SET minimum_order = ? WHERE product_id = ?");
                $stmt->bind_param("ii", $minVal, $pid);
                $stmt->execute();
                $stmt->close();
                break;
        }
    }
    
    header("Location: index.php?page=bulk_editor");
    exit;
}

// UPLOAD IMAGE
if ($action === 'upload') {
    $pid = (int) ($_POST['pid'] ?? 0);
    
    if ($pid > 0 && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $fileName = 'prod_' . $pid . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Delete old picture record
            $conn->query("DELETE FROM product_picture WHERE product_id = $pid");
            
            // Insert new picture
            $stmt = $conn->prepare("INSERT INTO product_picture (product_id, picture) VALUES (?, ?)");
            $stmt->bind_param("is", $pid, $fileName);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    header("Location: index.php?page=bulk_editor");
    exit;
}

// DELETE PRODUCT
if ($action === 'delete') {
    $pid = (int) ($_POST['pid'] ?? 0);
    
    if ($pid > 0) {
        $conn->query("DELETE FROM product_price WHERE product_id = $pid");
        $conn->query("DELETE FROM product_stock WHERE product_id = $pid");
        $conn->query("DELETE FROM product_picture WHERE product_id = $pid");
        
        $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $stmt->close();
    }
    
    header("Location: index.php?page=bulk_editor&deleted=1");
    exit;
}

// Default redirect
header("Location: index.php?page=bulk_editor");
exit;