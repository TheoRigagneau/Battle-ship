<?php
session_start();
header('refresh:1');
$fichier = "etat_joueurs.json";
$etat = json_decode(file_get_contents($fichier), true);

if ($etat["j1"] === session_id()) {
    $role = "Joueur 1";
} elseif ($etat["j2"] === session_id()) {
    $role = "Joueur 2";
} else {
    $role = "Aucun rôle";
}

$grid = 
[
  [3,0,0,0,0,0,0,2,2,0],
  [3,0,0,0,0,0,0,0,0,0],
  [3,0,0,0,0,0,0,0,0,0],
  [0,0,0,0,0,2,2,0,0,0],
  [0,0,0,0,0,5,0,0,0,4],
  [0,0,0,0,0,5,0,0,0,4],
  [0,0,0,0,0,5,0,0,0,4],
  [3,3,3,0,0,5,0,0,0,4],
  [0,0,0,0,0,5,0,0,0,0],
  [0,0,0,0,0,0,0,0,0,0]
];

$enemygrid =
[
  [0,0,4,4,4,4,0,0,0,0],
  [0,0,0,0,0,0,0,3,0,0],
  [2,2,2,2,0,0,0,3,0,0],
  [0,0,0,0,0,0,0,3,0,0],
  [0,5,0,0,0,0,0,0,0,0],
  [0,5,0,0,0,0,0,0,0,0],
  [0,5,0,0,0,2,2,2,0,0],
  [0,5,0,0,0,0,0,0,0,0],
  [0,0,0,0,0,0,0,0,0,0],
  [0,0,0,4,4,4,4,0,0,0]
];
if ($role =="Joueur 1")
{
  $mygrid=$grid;
  $theirgrid=$enemygrid;
}
else if ($role =="Joueur 2")
{
  $mygrid=$enemygrid;
  $theirgrid=$grid;
}

$dict = ['A'=>0,'B'=>1,'C'=>2,'D'=>3,'E'=>4,'F'=>5,'G'=>6,'H'=>7,'I'=>8,'J'=>9];
$letters = range('A','J');
$coord = $_GET['coord'] ?? null;
$shots = [];

if ($coord && !in_array($coord, $shots)) {
    $shots[] = $coord;
}
$fichier = "etat_joueurs.json";
$etat = json_decode(file_get_contents($fichier), true);

function save_state($file, $data) {
  file_put_contents($file, json_encode($data));
}

if (isset($_POST["reset_total"])) {
  $etat = ["j1" => null, "j2" => null, "start" =>false];
  save_state($GLOBALS['fichier'], $etat);

  session_unset();
  session_destroy();
}
if ($etat["start"] === false) {
  header("Location: pregame.php");
  exit;
}


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
            $coord1 = $dict[$letter];
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
            $coord1 = $dict[$letter];
            $coord2 = $i-1;

            $current = $letter.$i;

            $color = "";
            if (in_array($current, $shots)) {
                if ($theirgrid[$coord1][$coord2] == 0)
                    $color = "miss";
                else
                    $color = "hit";
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
