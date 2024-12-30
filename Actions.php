<?php 
require_once("DBConnection.php");
session_start(); // Start the session



Class Actions {
 
    function login(){
        global $conn;
        extract($_POST);
        $sql = "SELECT * FROM admin_list where username = '{$username}' and `password` = '{$password}' ";
        @$qry = $conn->query($sql)->fetch_assoc();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
  function customer_login() {
    global $conn; // Ensure the connection variable is accessible

    extract($_POST);
    $sql = "SELECT * FROM customer_list WHERE username = '{$username}' AND `password` = '" . md5($password) . "'";
    
    // Check for errors
    if (!$qry = $conn->query($sql)) {
        $resp['status'] = "failed";
        $resp['msg'] = "Database query failed: " . $conn->error; // Get error message
        return json_encode($resp);
    }

    $qry = $qry->fetch_assoc();
    
    if (!$qry) {
        $resp['status'] = "failed";
        $resp['msg'] = "Invalid username or password.";
    } else {
        if ($qry['status'] != 1) {
            $resp['status'] = "failed";
            $resp['msg'] = "Your account has been blocked by the management. Contact the management to settle.";
        } else {
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach ($qry as $k => $v) {
                if (!is_numeric($k)) {
                    $_SESSION[$k] = $v;
                }
            }
        }
    }

    return json_encode($resp);
}

    
    function logout(){
        session_destroy();
        header("location:./admin");
    }
    function customer_logout(){
        session_destroy();
        header("location:./");
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$_SESSION['admin_id']}'";
            @$save = $conn->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '. $conn->error;
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function update_credentials_customer(){
        global $conn;
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `customer_list` set {$data} where customer_id = '{$_SESSION['customer_id']}'";
            @$save = $conn->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '. $conn->error;
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_category(){
        global $conn;
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `category_list` (`name`,`status`)VALUES('{$name}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `category_list` set {$data} where `category_id` = '{$id}' ";
        }
        @$check= $conn->query("SELECT COUNT(category_id) as count from `category_list` where `name` = '{$name}' ".($id > 0 ? " and category_id != '{$id}'" : ""))->fetch_assoc()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Category Name already exists.';
        }else{
            @$save = $conn->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Category successfully saved.";
                else
                    $resp['msg'] = "Category successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Category Failed.";
                else
                    $resp['msg'] = "Updating Category Failed.";
                $resp['error']= $conn->error;
            }
        }
        return json_encode($resp);
    }
    function delete_category(){
        global $conn;
        extract($_POST);

        @$delete = $conn->query("DELETE FROM `category_list` where category_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function update_stat_cat(){
        global $conn;
        extract($_POST);
        @$update = $conn->query("UPDATE `category_list` set `status` = '{$status}' where category_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category Status successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function save_fee(){
        global $conn;
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `fees_list` (`location`,`amount`,`status`)VALUES('{$location}','{$amount}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `fees_list` set {$data} where `fee_id` = '{$id}' ";
            
        }
        @$check= $conn->query("SELECT COUNT(fee_id) as count from `fees_list` where `location` = '{$location}' ".($id > 0 ? " and fee_id != '{$id}'" : ""))->fetch_assoc()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Location already exists.';
        }else{
            @$save = $conn->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Delivery Fee successfully saved.";
                else
                    $resp['msg'] = "Delivery Fee successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Delivery Fee Failed.";
                else
                    $resp['msg'] = "Updating Delivery Fee Failed.";
                $resp['error']= $conn->error;
            }
        }
        return json_encode($resp);
    }
    function delete_fee(){
        global $conn;
        extract($_POST);

        @$delete = $conn->query("DELETE FROM `fees_list` where fee_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Delivery Fee successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function update_stat_fee(){
        global $conn;
        extract($_POST);
        @$update = $conn->query("UPDATE `fees_list` set `status` = '{$status}' where fee_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Delivery Fee Status successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function save_stock(){
        global $conn;
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `inventory_list` (`product_id`,`quantity`)VALUES('{$product_id}','{$quantity}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `inventory_list` set {$data} where `inventory_id` = '{$id}' ";
        }
      
        @$save = $conn->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id))
                $resp['msg'] = "Delivery stock successfully saved.";
            else
                $resp['msg'] = "Delivery stock successfully updated.";
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Delivery stock Failed.";
            else
                $resp['msg'] = "Updating Delivery stock Failed.";
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function delete_stock(){
        extract($_POST);

        @$delete = $conn->query("DELETE FROM `inventory_list` where stock_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Delivery stock successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function save_user() {
        global $conn;
        extract($_POST);
        $data = "";
        $cols = array();  // Initialize the arrays
        $values = array(); 
    
        // Loop through POST data
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id', 'type'))) { // Exclude 'id' and 'type' from processing
                if (!empty($id)) {
                    // If updating, build the SET part
                    if (!empty($data)) $data .= ",";
                    $data .= " `{$k}` = '{$v}' ";
                } else {
                    // If inserting, prepare columns and values
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        
        // Always include password when inserting
        if (empty($id)) { // For new user, add password field
            $cols[] = 'password';
            $values[] = "'" . md5($password) . "'"; // Use the provided password
        } else {
            if (isset($_POST['password']) && !empty($_POST['password'])) { // Update the password only if provided in POST
                $data .= ", `password` = '" . md5($_POST['password']) . "'";
            }
        }
    
        // Create SQL statement
        if (empty($id)) {
            // Insert statement
            $sql = "INSERT INTO `admin_list` (" . implode(',', $cols) . ") VALUES (" . implode(',', $values) . ")";
        } else {
            // Update statement
            $sql = "UPDATE `admin_list` SET {$data} WHERE admin_id = '{$id}'";
        }
    
        // Check for existing username
        @$check = $conn->query("SELECT count(admin_id) as `count` FROM admin_list WHERE `username` = '{$username}' " . ($id > 0 ? " AND admin_id != '{$id}' " : ""))->fetch_assoc()['count'];
    
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        } else {
            @$save = $conn->query($sql);
            
            if ($save) {
                $resp['status'] = 'success';
                if (empty($id))
                    $resp['msg'] = 'New Admin User successfully saved.';
                else
                    $resp['msg'] = 'Admin User Details successfully updated.';
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving Admin User Details Failed. Error: ' . $conn->error;
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    
    
    function delete_user(){
        
        global $conn;
        extract($_POST);

        @$delete = $conn->query("DELETE FROM `admin_list` where admin_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Admin User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function save_customer(){
        global $conn;  // Use the global connection variable
    
        extract($_POST);
        $data = "";
        $cols = [];
        $values = [];
    
        // Loop through POST data
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){  // Exclude 'id' from the query
                if($k == 'password'){  // Encrypt password
                    if(empty($v)) {
                        continue;
                    } else {
                        $v = md5($v);  // MD5 encryption
                    }
                }
                if(!empty($id)){  // Update case
                    if(!empty($data)) $data .= ", ";
                    $data .= "`{$k}` = '{$v}'";
                } else {  // Insert case
                    $cols[] = "`{$k}`";
                    $values[] = "'{$v}'";
                }
            }
        }
    
        // Prepare insert query
        if(empty($id) && isset($cols) && isset($values)){
            $data = "(".implode(',', $cols).") VALUES (".implode(',', $values).")";
        }
    
        // Check for existing username
        $check_query = "SELECT count(customer_id) as `count` FROM customer_list WHERE `username` = '{$username}'";
        if(!empty($id)){
            $check_query .= " AND customer_id != '{$id}'";  // Exclude the current record when updating
        }
    
        // Execute the check query
        $check = $conn->query($check_query)->fetch_assoc()['count'];
    
        // Username exists
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        } else {
            // Insert or update query
            if(empty($id)){  // Insert
                $sql = "INSERT INTO `customer_list` {$data}";
            } else {  // Update
                $sql = "UPDATE `customer_list` SET {$data} WHERE customer_id = '{$id}'";
            }
    
            // Execute the query
            if($conn->query($sql)){
                $resp['status'] = 'success';
                $resp['msg'] = empty($id) ? 'Account successfully created.' : 'Account details successfully updated.';
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving details failed. Error: ' . $conn->error;
                $resp['sql'] = $sql;  // Include the SQL for debugging purposes
            }
        }
    
        // Return the response as JSON
        return json_encode($resp);
    }
    
    
    function delete_customer(){
        global $conn;
        extract($_POST);

        @$delete = $conn->query("DELETE FROM `customer_list` where customer_id= '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Customer successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
    function save_product() {
        global $conn;
        extract($_POST);
        $name = trim($name);
        $id = intval($id);
        $quantity = intval($quantity); // Ensure quantity is an integer
    
        // Check if product name already exists (case-insensitive)
        $check = $conn->query("SELECT COUNT(product_id) as `count` 
                              FROM `product_list` 
                              WHERE LOWER(`name`) = LOWER('$name')"
                              . ($id > 0 ? " AND product_id != $id" : ''))->fetch_assoc()['count'];
    
        if ($check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Product Name already exists.";
        } else {
            $data = "";
            foreach ($_POST as $k => $v) {
                if (!in_array($k, array('id', 'thumbnail', 'img', 'quantity'))) {
                    $v = $conn->real_escape_string($v);
                    if (empty($id)) {
                        $columns[] = "`{$k}`";
                        $values[] = "'{$v}'";
                    } else {
                        if (!empty($data)) $data .= ", ";
                        $data .= " `{$k}` = '{$v}'";
                    }
                }
            }
    
            if (isset($columns) && isset($values)) {
                $data = "(".implode(",", $columns).") VALUES (".implode(",", $values).")";
            }
    
            // Build SQL query for insert or update
            $sql = empty($id) ? "INSERT INTO `product_list` {$data}" : "UPDATE `product_list` SET {$data} WHERE product_id = '{$id}'";
    
            // Execute the query
            $save = $conn->query($sql);
            if ($save) {
                $resp['status'] = 'success';
                $resp['msg'] = empty($id) ? 'Product Successfully added.' : 'Product Successfully updated.';
                $pid = empty($id) ? $conn->insert_id : $id;
    
                // Insert into inventory_list table
                $inventory_sql = empty($id) ? 
                    "INSERT INTO `inventory_list` (`product_id`, `quantity`, `type`, `date_created`) VALUES ('{$pid}', '{$quantity}', '2', NOW())" :
                    "UPDATE `inventory_list` SET `quantity` = '{$quantity}', `date_created` = NOW() WHERE `product_id` = '{$pid}'";
    
                $inventory_save = $conn->query($inventory_sql);
                if (!$inventory_save) {
                    $resp['status'] = 'failed';
                    $resp['msg'] = 'Failed to update inventory: ' . $conn->error;
                    return json_encode($resp);
                }
    
                // Handling thumbnails
                if (isset($_FILES['thumbnail']) && !empty($_FILES['thumbnail']['tmp_name'])) {
                    $thumb_file = $_FILES['thumbnail']['tmp_name'];
                    $thumb_fname = $pid.'.png';
    
                    if (!is_uploaded_file($thumb_file)) {
                        $resp['status'] = 'failed';
                        $resp['msg'] = 'Thumbnail was not properly uploaded.';
                        return json_encode($resp);
                    }
    
                    $target_dir = __DIR__.'/uploads/thumbnails/';
                    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
    
                    $thumb_path = $target_dir.$thumb_fname;
    
                    if (move_uploaded_file($thumb_file, $thumb_path)) {
                        $resp['msg'] .= ' Thumbnail uploaded successfully.';
                    } else {
                        $resp['status'] = 'failed';
                        $resp['msg'] = 'Failed to move uploaded thumbnail.';
                        return json_encode($resp);
                    }
                }
    
                // Handling multiple images
                if (isset($_FILES['img']) && count($_FILES['img']['tmp_name']) > 0) {
                    $img_dir = __DIR__.'/uploads/images/'.$pid;
                    if (!is_dir($img_dir)) mkdir($img_dir, 0755, true);
    
                    for ($i = 0; $i < count($_FILES['img']['tmp_name']); $i++) {
                        if (!empty($_FILES['img']['tmp_name'][$i])) {
                            $img_file = $_FILES['img']['tmp_name'][$i];
                            $ex = pathinfo($_FILES['img']['name'][$i], PATHINFO_FILENAME);
                            $img_fname = $ex.'.png';
    
                            if (!is_uploaded_file($img_file)) {
                                $resp['status'] = 'failed';
                                $resp['msg'] = 'One of the images was not properly uploaded.';
                                return json_encode($resp);
                            }
    
                            $img_path = $img_dir.'/'.$img_fname;
    
                            if (move_uploaded_file($img_file, $img_path)) {
                                $resp['msg'] .= ' Image '.$i.' uploaded successfully.';
                            } else {
                                $resp['status'] = 'failed';
                                $resp['msg'] = 'Failed to move image '.$i.'.';
                                return json_encode($resp);
                            }
                        }
                    }
                }
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'An error occurred: '.$conn->error;
            }
        }
    
        return json_encode($resp);
    }
    
    
    
    function delete_product(){
        global $conn;
        extract($_POST);
        @$delete = $conn->query("DELETE FROM `product_list` where product_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Employee successfully deleted.';
            if(is_file(__DIR__.'/uploads/thumbnails/'.$id.'.png'))
                unlink(__DIR__.'/uploads/thumbnails/'.$id.'.png');
            if(is_dir(__DIR__.'/uploads/images/'.$id)){
                $scan = scandir(__DIR__.'/uploads/images/'.$id);
                foreach($scan as $img){
                    if(!in_array($img,array('.','..'))){
                        unlink(__DIR__.'/uploads/images/'.$id.'/'.$img);
                    }
                }
                rmdir(__DIR__.'/uploads/images/'.$id);
            }
        }else{
            $resp['status']='failed';
            $resp['msg'] = 'An error occure. Error: '. $conn->error;
        }
        return json_encode($resp);
    }
    function save_attendance(){
        global $conn;
        extract($_POST);
        @$employee_id = $conn->query("SELECT employee_id FROM `employee_list` where `employee_code` = '{$employee_code}'")->fetch_assoc()['employee_id'];
        if($employee_id > 0){
            $check = $conn->query("SELECT count(attendance_id) as `count` FROM `attendance_list` where `employee_id` = '{$employee_id}' and `att_type_id` = '{$att_type_id}' and date(`date_created`) = '".date("Y-m-d",strtotime($date_created))."' ")->fetch_assoc()['count'];
            if($check > 0){
                $resp['status'] = 'failed';
                $resp['msg'] = "You already have ".$att_type. " record today.";
            }else{
            $sql = "INSERT INTO `attendance_list` (`employee_id`,`att_type_id`,`date_created`) VALUES ('{$employee_id}','{$att_type_id}','{$date_created}')";
            @$save = $conn->query($sql);
            if($save){
                $resp['status'] = 'success';
            } else{
                $resp['status'] = 'failed';
                $resp['msg'] = "An error occured. Error: ".  $conn->error;
            }
        }

        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Uknown Employee Code.";
        }
        return json_encode($resp);
    }
    function delete_img(){
        global $conn;

        extract($_POST);
        if(is_file(__DIR__.$path)){
            unlink(__DIR__.$path);
        }
        $resp['status'] = 'success';
        return json_encode($resp);
    }
    function add_to_cart() {
        global $conn;
        global $conn;
        extract($_POST);
    
        // Log missing product_id or customer_id
        if (!isset($product_id) || !isset($_SESSION['customer_id'])) {
            error_log("Missing product_id or customer_id in add_to_cart");
            return json_encode(['status' => 'failed', 'error' => 'Missing product_id or customer_id']);
        }
    
        $customer_id = $_SESSION['customer_id'];
        $check = $conn->query("SELECT count(product_id) as `count` FROM `cart_list` where `product_id` = '{$product_id}' and `customer_id` = '{$customer_id}'")->fetch_assoc()['count'];
    
        if ($check > 0) {
            $sql = "UPDATE `cart_list` set `quantity` = `quantity`+1 where `product_id` = '{$product_id}' and `customer_id` = '{$customer_id}'";
        } else {
            $sql = "INSERT INTO `cart_list` (`product_id`, `customer_id`, `quantity`) VALUES ('{$product_id}', '{$customer_id}', '{$quantity}')";
        }
    
        $save = $conn->query($sql);
        if ($save) {
            $resp['status'] = 'success';
            $count = $conn->query("SELECT SUM(quantity) as total FROM `cart_list` where `customer_id` = '{$customer_id}'")->fetch_assoc()['total'];
            $resp['cart_count'] = $count;
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $conn->error; // Log database errors
            $resp['sql'] = $sql;
        }
    
        return json_encode($resp);
    }
    
    
    function update_cart(){
        global $conn;
        extract($_POST);
        $in = $conn->query("SELECT SUM(quantity) as `in` from inventory_list where product_id = '{$product_id}' and `type` = 1")->fetch_assoc()['in'];
        $out = $conn->query("SELECT SUM(quantity) as `out` from inventory_list where product_id = '{$product_id}' and `type` = 2")->fetch_assoc()['out'];
        $available =  $out;
        $sql = "UPDATE `cart_list` set `quantity` = '{$quantity}' where `product_id` = '{$product_id}' and `customer_id` = '{$customer_id}'";
        if($quantity > $available){
            $resp['status'] ='failed';
            $resp['msg'] ="Product has only 0 item/s left.";
            $resp['available'] =$available;
        }else{ 
            $save = $conn->query($sql);
            if($save){
                $resp['status'] ='success';
                $count = $conn->query("SELECT SUM(quantity) as total FROM `cart_list` where `customer_id` = '{$customer_id}'")->fetch_assoc()['total'];
                $resp['cart_count'] = $count;
            }else{
                $resp['status'] ='failed';
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_from_cart(){
        global $conn;
        extract($_POST);
        $customer_id = $_SESSION['customer_id'];
        $sql = "DELETE FROM `cart_list` where `product_id` = '{$id}' and `customer_id` = '{$customer_id}'";
        $delete = $conn->query($sql);
        if($delete){
            $resp['status'] ='success';
            $count = $conn->query("SELECT SUM(quantity) as total FROM `cart_list` where `customer_id` = '{$customer_id}'")->fetch_assoc()['total'];
            $resp['cart_count'] = $count;
        }else{
            $resp['status'] ='failed';
            $resp['sql'] =$sql;
        }
        return json_encode($resp);
    }
    function place_order(){
        global $conn;
        extract($_POST);
    
        if(!isset($_SESSION['customer_id'])) {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Customer not logged in.';
            return json_encode($resp);
        }
    
        $customer_id = $_SESSION['customer_id'];
        $code = "";
        
        // Generate unique transaction code
        while(true){
            $code = sprintf("%.9d", mt_rand(1,9999999999));
            $check = $conn->query("SELECT count(order_id) as `count` FROM `order_list` WHERE transaction_code = '{$code}'")->fetch_assoc()['count'];
            if($check <= 0) break;
        }
    
        // Insert order into order_list
        $sql = "INSERT INTO `order_list` (`customer_id`,`transaction_code`,`delivery_address`,`total_amount`,`fee_id`) 
                VALUES('{$customer_id}','{$code}','{$delivery_address}','{$total_amount}','{$fee_id}')";
        
        $save = $conn->query($sql);
        if($save){
            $resp['status'] = 'success';
            $last_id = $conn->insert_id;
    
            // Fetch cart items
            $cart = $conn->query("SELECT * FROM `cart_list` WHERE `customer_id` = '{$customer_id}'");
            while($row = $cart->fetch_assoc()){
                // Insert into inventory_list first
                $inventory_sql = "INSERT INTO `inventory_list` (`product_id`, `quantity`, `type`) 
                                  VALUES ('{$row['product_id']}', '{$row['quantity']}', '2')";
                $save_inventory = $conn->query($inventory_sql);
                if(!$save_inventory){
                    $resp['status'] = 'failed';
                    $resp['msg'] = 'Failed to save inventory. Error: ' . $conn->error;
                    return json_encode($resp);
                }
                
                // Get the last inserted inventory_id
                $inventory_id = $conn->insert_id;
                
                // Insert into order_items
                $data_items = "('{$last_id}', '{$row['product_id']}', '{$row['quantity']}', '{$inventory_id}')";
                $order_items_sql = "INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `inventory_id`) VALUES {$data_items}";
                $save_order_items = $conn->query($order_items_sql);
                if(!$save_order_items){
                    $resp['status'] = 'failed';
                    $resp['msg'] = 'Failed to save order items. Error: ' . $conn->error;
                    return json_encode($resp);
                }
            }
    
            // Clear cart after placing order
            $conn->query("DELETE FROM `cart_list` WHERE `customer_id` = '{$customer_id}'");
    
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Order successfully placed.';
    
            // Redirect to payment page
            $resp['redirect'] = 'payment.php?order_id=' . $last_id;
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Order placement failed. Error: ' . $conn->error;
        }
    
        return json_encode($resp);
    }
    
    
    function update_order_status(){
        global $conn;
        extract($_POST);
        $sql = "UPDATE `order_list` set `status` = '{$status}' where `order_id` = '{$order_id}' ";
        @$update = $conn->query($sql);
        if($update){
            $resp['status']='success';
            $resp['msg'] = "Order Status successfully updated";
            $resp['return_status'] = $status;
        }else{
            $resp['status']='failed';
            $resp['msg'] = "Failed to Update status. Error: ". $conn->error;
            $resp['sql'] = $sql;
        }
        return json_encode($resp);
    }
    function update_customer_status(){
        global $conn;
        extract($_POST);
        $sql = "UPDATE `customer_list` set `status` = '{$status}' where `customer_id` = '{$id}' ";
        @$update = $conn->query($sql);
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['msg'] = "Customer Status successfully updated";
            $_SESSION['flashdata']['type'] = "success";
        }else{
            $resp['status']='failed';
            $resp['msg'] = "Failed to Update status. Error: ". $conn->error;
            $resp['sql'] = $sql;
        }
        return json_encode($resp);
    }
    function delete_transaction(){
        global $conn;
        extract($_POST);
        $inv_ids = array();
        $qry = $conn->query("SELECT * FROM order_items where order_id = '{$id}'");
        while($row = $qry->fetch_assoc()){
            $inv_ids[] = $row['inventory_id'];
        }
        @$delete = $conn->query("DELETE FROM `order_list` where order_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Order successfully deleted.';
            if(count($inv_ids) > 0){
                $ids = implode(",",$inv_ids);
                $conn->query("DELETE FROM `inventory_list` where inventory_id in ({$ids})");
            }
        }else{
            $resp['status']='failed';
            $resp['error']= $conn->error;
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'customer_login':
        echo $action->customer_login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'customer_logout':
        echo $action->customer_logout();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'update_credentials_customer':
        echo $action->update_credentials_customer();
    break;
    case 'save_category':
        echo $action->save_category();
    break;
    case 'delete_category':
        echo $action->delete_category();
    break;
    case 'update_stat_cat':
        echo $action->update_stat_cat();
    break;
    case 'save_fee':
        echo $action->save_fee();
    break;
    case 'delete_fee':
        echo $action->delete_fee();
    break;
    case 'save_stock':
        echo $action->save_stock();
    break;
    case 'delete_stock':
        echo $action->delete_stock();
    break;
    case 'update_stat_fee':
        echo $action->update_stat_fee();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'save_customer':
        echo $action->save_customer();
    break;
    case 'delete_customer':
        echo $action->delete_customer();
    break;
    case 'save_product':
        echo $action->save_product();
    break;
    case 'delete_product':
        echo $action->delete_product();
    break;
    case 'save_attendance':
        echo $action->save_attendance();
    break;
    case 'delete_img':
        echo $action->delete_img();
    break;
    case 'add_to_cart':
        echo $action->add_to_cart();
    break;
    case 'update_cart':
        echo $action->update_cart();
    break;
    case 'delete_from_cart':
        echo $action->delete_from_cart();
    break;
    case 'place_order':
        echo $action->place_order();
    break;
    case 'update_order_status':
        echo $action->update_order_status();
    break;
    case 'update_customer_status':
        echo $action->update_customer_status();
    break;
    case 'delete_transaction':
        echo $action->delete_transaction();
    break;
    default:
    // default action here
    break;
}