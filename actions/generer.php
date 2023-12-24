<?php
include('connect.php');
$nbrP = $_POST['nbrP'];
$CAISSIER = $_POST['CAISSIER'];
$temps = $_POST['temps'];
$globalTotal = $_POST['global-total'];
$sql = "select * from `cafe`";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $Nb_Tickets=$row['Nb_Tickets']+1;
    }
}

// Suand on génére Ticket on a besoin de faire plusieurs traitement insersions et updates dans les différents tableaux de la base de données : 



// ******Cafe******
$conn->query(" update cafe set Nb_Tickets=Nb_Tickets+1 ");

// ******Caissiers******
$conn->query(" update caissiers set Nb_Commandes=Nb_Commandes+1 where Username='$CAISSIER' ");

// ******Ccommande******
$conn->query(" insert into `commande` values ('$Nb_Tickets','$temps','$nbrP','$CAISSIER','$globalTotal') ");

$datetime = DateTime::createFromFormat('d/m/Y - H:i:s', $temps);
// Get only the date part
$date = $datetime->format('Y-m-d');


$sqlCheck = "SELECT * FROM recettes WHERE date = '$date'";
$resultCheck = $conn->query($sqlCheck);
if ($resultCheck->num_rows > 0) {
    $oldCFA = $resultCheck->fetch_assoc()["CFA"];

    // Row with the specified date already exists, perform update
    $conn->query(" UPDATE  `recettes` SET CFA =$oldCFA+$globalTotal WHERE date = '$date' ");
} else {
    $conn->query(" insert into `recettes` values ('$date','$globalTotal') ");
}



// ******Produits et ligne de recettes ******
for ($i=1 ; $i<=$nbrP ; $i++){
    $label = $_POST['Prd'.$i];
    $quantite = $_POST['Qnt'.$i];
    $conn->query(" update produits set Nb_Commande=Nb_Commande+'$quantite' where Label='$label'");
    $conn->query("insert into `ligne_commande` (Num_Commande,Label_Produit,quantite) values ('$Nb_Tickets','$label','$quantite') ");
}

echo'
    <script>
        window.location ="../partitions/gene-ticket.php";
    </script>
';


?>