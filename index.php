<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau 10x10</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body class="p-4">

  <h2 class="mb-3">Battle-ships</h2>

  <table class="table table-bordered table-striped text-center align-middle">
    <thead class="table-dark">
      <tr>
        <th></th>
        <?php for($i = 1; $i <= 10; $i++): ?>
          <th><?= $i ?></th>
        <?php endfor; ?>
      </tr>
    </thead>

    <tbody>
      <?php 
      session_start();
      $grid = 
      [
        [3, 0, 0, 0, 0, 0, 0, 2, 2, 0],
        [3, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [3, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 2, 2, 0, 0, 0],
        [0, 0, 0, 0, 0, 5, 0, 0, 0, 4],
        [0, 0, 0, 0, 0, 5, 0, 0, 0, 4],
        [0, 0, 0, 0, 0, 5, 0, 0, 0, 4],
        [3, 3, 3, 0, 0, 5, 0, 0, 0, 4],
        [0, 0, 0, 0, 0, 5, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
      ];
      $dict =
      [
        'A' => 0,
        'B' => 1,
        'C' => 2,
        'D' => 3,
        'E' => 4,
        'F' => 5,
        'G' => 6,
        'H' => 7,
        'I' => 8,
        'J' => 9
      ];
      $letters = range('A', 'J');
      $coord = $_GET['coord'] ?? null;

      if ($coord)
      {
        $coord1=$dict[$coord[0]];
        $coord2 = intval(substr($coord, 1))-1;
        $_SESSION['shots'][] = $coord;
      }

      foreach($letters as $letter): ?>
        <tr>
          <th><?= $letter ?></th>

          <?php for($i = 1; $i <= 10; $i++): 
            $current_coords = $letter . $i;
            $color = "";
            if ($coord== $current_coords) 
            {
              if ($grid[$coord1][$coord2]=='0')
              {
                $color = "miss";
              }
              else
              {
                $color = "hit";
              }
            }
            ?>
            <td class="<?= $color ?>">
              <a class="button" href="?coord=<?= $letter . $i ?>"></a>              
            </td>
          <?php endfor; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php  
      ?>
</body>
</html>