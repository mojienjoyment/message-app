<?php
/* ════════════════════════════════════════════════
   PART 1 — CONNECTION (very top, before anything else)
   ════════════════════════════════════════════════ */
require 'config.php';

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$db;charset=utf8mb4",
    $user,
    $pass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed');
}

/* ════════════════════════════════════════════════
   PART 2 — SAVE THE NAME (only runs on form submit)
   ════════════════════════════════════════════════ */
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');

  if ($name === '' || mb_strlen($name) > 60) {
    http_response_code(400);
    exit('Missing or too long: name');
  }

  $stmt = $pdo->prepare('INSERT INTO names (name) VALUES (:name)');
  $stmt->execute(['name' => $name]);
  $saved = true;
}

/* ════════════════════════════════════════════════
   PART 3 — FETCH INVITATION CODES (runs every visit)
   ════════════════════════════════════════════════ */
$codes = $pdo->query('SELECT * FROM invitation_codes')->fetchAll();
?>

<!-- ════════════════════════════════════════════════
     PART 4 — PRINT THE RESULTS (HTML goes after all PHP above)
     ════════════════════════════════════════════════ -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invitation codes</title>
  <style>
    body { font-family: sans-serif; padding: 2rem; }
    table { border-collapse: collapse; font-family: monospace; margin-top: 1rem; }
    th, td { border: 1px solid #ccc; padding: .4rem .8rem; text-align: left; }
    th { background: #f0f0f0; }
    .saved { color: green; font-weight: bold; }
  </style>
</head>
<body>

  <?php if ($saved): ?>
    <p class="saved">Name saved ✓</p>
  <?php endif; ?>

  <h2>Invitation codes (<?= count($codes) ?>)</h2>

  <?php if (!$codes): ?>
    <p>Table is empty.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <?php foreach (array_keys($codes[0]) as $column): ?>
            <th><?= htmlspecialchars($column) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($codes as $row): ?>
          <tr>
            <?php foreach ($row as $value): ?>
              <td><?= htmlspecialchars((string) ($value ?? '')) ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</body>
</html>