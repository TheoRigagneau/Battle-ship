<?php
session_start();
include_once('sql-connect.php');

$sql = new SqlConnect(); 
$grid1 = "SELECT * FROM joueur1";  //met la commande  a réaliser dans le sql
$stmt = $sql->db->prepare($grid1); //récupère la table joueur 1
$stmt->execute(); //execute la commande

$grid1 = $stmt->fetchAll(PDO::FETCH_ASSOC);  //affecte à $grid la grille joueur 1

$mygrid = [];
foreach($grid1 as $row) {
    $letter = $row['gridid'][0]; //récupère la lettre de gridID
    $number = intval(substr($row['gridid'],1)) - 1; //récupère le nombre présent après la lettre 
    $mygrid[$letter][$number] = $row['checked'];
}
$grid2 = "SELECT * FROM joueur1";
$stmt = $sql->db->prepare($grid2);
$stmt->execute();

$grid2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$enemygrid = [];
foreach($grid1 as $row) {
    $letter = $row['gridid'][0];
    $number = intval(substr($row['gridid'],1)) - 1; 
    $enemygrid[$letter][$number] = $row['checked'];
}

$fichier = "etat_joueurs.json";
$etat = json_decode(file_get_contents($fichier), true);

if ($etat["j1"] === session_id()) { //si l'utilisateur est j1 = donne role et shots
    $role = "Joueur 1";
    $shots = "shots_j1";
} elseif ($etat["j2"] === session_id()) {
    $role = "Joueur 2";
    $shots = "shots_j2";
} else {
    $role = "Aucun rôle";
}
$shotscoord = $etat[$shots] ?? []; //recup les shots dans etat_joueurs

if ($role =="Joueur 1")
{
  $mygrid=$grid1; //affecte la grille 1 en tant que grille de l'utilisateur
  $theirgrid=$grid2; //affecte la grille 2 en tant que grille de l'adversaire
}
else if ($role =="Joueur 2")
{
  $mygrid=$grid2;
  $theirgrid=$grid1;
}
function save_state($file, $data) {
  file_put_contents($file, json_encode($data));
}

$letters = range('A','J');
for ($i=0; $i<10; $i++) {
    foreach($letters as $letter) {
        if (!isset($theirgrid[$letter][$i])) { //initialise les grilles 
            $theirgrid[$letter][$i] = 0;
        }
        if (!isset($mygrid[$letter][$i])) {
            $mygrid[$letter][$i]= 0;
        }
    }
}

$coord = $_GET['coord'] ?? null; //récupère la coordonnée cliqué

if ($coord && !in_array($coord, $shotscoord)) {
    $shotscoord[] = $coord;
    $etat[$shots]= $shotscoord;
    save_state($GLOBALS['fichier'], $etat);
    $_POST['cell'] = $coord;
    include('click_case.php');
}


if (isset($_POST["reset_total"])) {
  $etat = ["j1" => null, "j2" => null, "start" =>false]; //reset les données
  save_state($GLOBALS['fichier'], $etat);

  session_unset();
  session_destroy();

  $sql = new SqlConnect();
    $tables = ['joueur1', 'joueur2'];

    foreach ($tables as $table) {
        $query = "UPDATE $table SET checked = 0"; //rénitialise les deux grilles
        $stmt = $sql->db->prepare($query);
        $stmt->execute();
    }
}
if ($etat["start"] === false) {
  header("Location: pregame.php"); //renvoie sur la page d'accueil
  exit;
}

header('refresh:5'); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Battle-ships</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style_index.css">
</head>

<body class="p-4">

<h2 class="mb-3">Battle-ships</h2>

<div class="row">

  <div class="col-6">
    <h4>Votre grille</h4>

    <table class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th></th>
          <?php for ($i=1;$i<=10;$i++): ?><th><?= $i ?></th><?php endfor; ?>
        </tr>
      </thead>

      <tbody>
        <?php foreach($letters as $letter): ?>
        <tr>
          <th><?= $letter ?></th>

          <?php for ($i=1;$i<=10;$i++): 
            $coord1 = $letter;
            $coord2 = $i-1;

            $color = "";
            if ($mygrid[$coord1][$coord2] != 0)
                $color = "ship";
          ?>
            <td class="<?= $color ?>">
              <a class="button"></a>
            </td>
          <?php endfor; ?>

        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>

  <div class="col-6">
    <h4>Grille adverse</h4>

    <table class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th></th>
          <?php for ($i=1;$i<=10;$i++): ?><th><?= $i ?></th><?php endfor; ?>
        </tr>
      </thead>

      <tbody>
        <?php foreach($letters as $letter): ?>
        <tr>
          <th><?= $letter ?></th>

          <?php for ($i=1;$i<=10;$i++): 
            $coord1 = $letter;
            $coord2 = $i-1;

            $current = $letter.$i;

            $color = "";
            if (in_array($current, $shotscoord)) {
                if ($theirgrid[$coord1][$coord2] == 0){
                    $color = "miss";
                }
                else{
                    $color = "hit";
                }
            }
          ?>
          <td class="<?= $color ?>">
            <a class="button" href="?coord=<?= $letter.$i ?>"></a>
          </td>
          <?php endfor; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<form method="post">
  <button type="submit" name="reset_total">
    ❌
    Fin de partie (RESET)
  </button>
</form>
</body>
</html>
