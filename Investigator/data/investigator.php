<?php  

// Get Investigator by ID
function getInvestigatorById($investigator_id, $conn){
   $sql = "SELECT * FROM investigators
           WHERE investigator_id = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$investigator_id]);

   if ($stmt->rowCount() == 1) {
     $investigator = $stmt->fetch();
     return $investigator;
   } else {
     return 0;
   }
}

function getAllCasesByInvestigator($conn, $investigator_id) {
  $stmt = $conn->prepare("SELECT * FROM investigations WHERE investigator_id=? ORDER BY created_at DESC");
  $stmt->execute([$investigator_id]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

