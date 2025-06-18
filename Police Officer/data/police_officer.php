<?php  

// Get Officer by ID
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


