<?php  

// Get investigator by ID
function getInvestigatorById($investigator_id, $conn){
   $sql = "SELECT * FROM investigators WHERE investigator_id=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$investigator_id]);

   if ($stmt->rowCount() == 1) {
     return $stmt->fetch();
   } else {
     return 0;
   }
}

// All investigators
function getAllInvestigators($conn){
   $sql = "SELECT * FROM investigators";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() >= 1) {
     return $stmt->fetchAll();
   } else {
     return 0;
   }
}

// Check if the username is unique
function unameIsUnique($uname, $conn, $investigator_id = 0){
   $sql = "SELECT username, investigator_id FROM investigators WHERE username=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$uname]);
   
   if ($investigator_id == 0) {
     return $stmt->rowCount() < 1 ? 1 : 0;
   } else {
     if ($stmt->rowCount() >= 1) {
       $investigator = $stmt->fetch();
       return $investigator['investigator_id'] == $investigator_id ? 1 : 0;
     } else {
       return 1;
     }
   }
}

// Delete investigator
function removeInvestigator($id, $conn){
   $sql = "DELETE FROM investigators WHERE investigator_id=?";
   $stmt = $conn->prepare($sql);
   return $stmt->execute([$id]) ? 1 : 0;
}

// Search investigators
function searchInvestigators($key, $conn){
   $key = preg_replace('/(?<!\\\)([%_])/', '\\\$1', $key);
   $likeKey = "%$key%";

   $sql = "SELECT * FROM investigators
           WHERE investigator_id LIKE ? 
           OR fname LIKE ?
           OR lname LIKE ?
           OR username LIKE ?
           OR employee_number LIKE ?
           OR phone_number LIKE ?
           OR qualification LIKE ?
           OR email_address LIKE ?
           OR address LIKE ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([
     $likeKey, $likeKey, $likeKey, $likeKey, $likeKey,
     $likeKey, $likeKey, $likeKey, $likeKey
   ]);

   return $stmt->rowCount() >= 1 ? $stmt->fetchAll() : 0;
}
