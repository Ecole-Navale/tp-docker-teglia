<?php
require 'db.php';

$restaurants = [
    'McDonalds' => "McDonald's",
'BurgerKing' => "Burger King",
'KFC' => "KFC",
'OTacos' => "O'Tacos"
];

$query = $pdo->query("
SELECT restaurant, COUNT(*) AS total
FROM votes
GROUP BY restaurant
");

$dbResults = $query->fetchAll();

$results = [];
foreach ($restaurants as $key => $label) {
    $results[$key] = 0;
}

foreach ($dbResults as $row) {
    if (isset($results[$row['restaurant']])) {
        $results[$row['restaurant']] = (int)$row['total'];
    }
}

$totalVotes = array_sum($results);

arsort($results);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Résultats des votes</title>
<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f8fafc;
    color: #1e293b;
}

.container {
    max-width: 850px;
    margin: 40px auto;
    padding: 20px;
}

.card {
    background: white;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    padding: 30px;
}

h1 {
    margin-top: 0;
}

.subtitle {
    color: #475569;
    margin-bottom: 24px;
}

table {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
    border-radius: 14px;
}

thead {
    background: #0f172a;
    color: white;
}

th, td {
    padding: 16px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

tbody tr:hover {
    background: #f8fafc;
}

.rank {
    font-weight: bold;
    width: 80px;
}

.total-box {
    margin: 18px 0 24px;
    background: #e0f2fe;
    color: #0c4a6e;
    border-radius: 12px;
    padding: 14px 18px;
    font-weight: bold;
}

.back-link {
    display: inline-block;
    margin-top: 22px;
    text-decoration: none;
    color: #2563eb;
    font-weight: bold;
}

.back-link:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">
<div class="card">
<h1>Résultats détaillés</h1>
<p class="subtitle">Classement actuel des fast-foods.</p>

<div class="total-box">
Total des votes : <?php echo $totalVotes; ?>
</div>

<table>
<thead>
<tr>
<th>#</th>
<th>Restaurant</th>
<th>Votes</th>
<th>Part</th>
</tr>
</thead>
<tbody>
<?php $rank = 1; ?>
<?php foreach ($results as $key => $count): ?>
<?php $percent = $totalVotes > 0 ? round(($count / $totalVotes) * 100, 1) : 0; ?>
<tr>
<td class="rank"><?php echo $rank; ?></td>
<td><?php echo htmlspecialchars($restaurants[$key]); ?></td>
<td><?php echo $count; ?></td>
<td><?php echo $percent; ?>%</td>
</tr>
<?php $rank++; ?>
<?php endforeach; ?>
</tbody>
</table>

<a class="back-link" href="index.php">← Retour au vote</a>
</div>
</div>
</body>
</html>
