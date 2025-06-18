<?php  

// Get officer by ID
function getOfficerById($officer_id, $conn){
   $sql = "SELECT * FROM officers
           WHERE officer_id=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$officer_id]);

   if ($stmt->rowCount() == 1) {
     $officer = $stmt->fetch();
     return $officer;
   }else {
    return 0;
   }
}

// All officers 
function getAllOfficers($conn){
   $sql = "SELECT * FROM officers";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() >= 1) {
     $officers = $stmt->fetchAll();
     return $officers;
   }else {
   	return 0;
   }
}

// Check if the username Unique
function unameIsUnique($uname, $conn, $officer_id=0){
   $sql = "SELECT username, officer_id FROM officers
           WHERE username=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$uname]);
   
   if ($officer_id == 0) {
     if ($stmt->rowCount() >= 1) {
       return 0;
     }else {
      return 1;
     }
   }else {
    if ($stmt->rowCount() >= 1) {
       $officer = $stmt->fetch();
       if ($officer['officer_id'] == $officer_id) {
         return 1;
       }else {
        return 0;
      }
     }else {
      return 1;
     }
   }
   
}

// DELETE
function removeOfficer($id, $conn){
   $sql  = "DELETE FROM officers
           WHERE officer_id=?";
   $stmt = $conn->prepare($sql);
   $re   = $stmt->execute([$id]);
   if ($re) {
     return 1;
   }else {
    return 0;
   }
}

// Search 
function searchOfficers($key, $conn){
   $key = preg_replace('/(?<!\\\)([%_])/', '\\\$1',$key);

   $sql = "SELECT * FROM officers
           WHERE officer_id LIKE ? 
           OR fname LIKE ?
           OR lname LIKE ?
           OR username LIKE ?
           OR employee_number LIKE ?
           OR phone_number LIKE ?
           OR qualification LIKE ?
           OR email_address LIKE ?
           OR address LIKE ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$key, $key, $key, $key, $key,$key, $key, $key, $key]);

   if ($stmt->rowCount() == 1) {
     $officers = $stmt->fetchAll();
     return $officers;
   }else {
    return 0;
   }
}
