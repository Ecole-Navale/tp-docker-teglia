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

$message = '';
$messageType = '';

if (isset($_GET['success']) && $_GET['success'] === '1') {
    $message = "Vote enregistré avec succès.";
    $messageType = 'success';
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'already_voted':
            $message = "Vous avez déjà voté récemment.";
            break;
        case 'invalid_choice':
            $message = "Choix invalide.";
            break;
        default:
            $message = "Une erreur est survenue.";
            break;
    }
    $messageType = 'error';
}

$winnerKey = null;
$winnerVotes = 0;
foreach ($results as $key => $count) {
    if ($count > $winnerVotes) {
        $winnerVotes = $count;
        $winnerKey = $key;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fast Food Vote</title>
<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    color: #1e293b;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.card {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    padding: 30px;
    margin-bottom: 24px;
}

h1, h2 {
    margin-top: 0;
}

.subtitle {
    color: #475569;
    margin-bottom: 24px;
}

.message {
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: bold;
}

.message.success {
    background: #dcfce7;
    color: #166534;
}

.message.error {
    background: #fee2e2;
    color: #991b1b;
}

.vote-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}

.vote-btn {
    border: none;
    border-radius: 16px;
    padding: 18px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    color: white;
    transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
}

.vote-btn:hover {
    transform: translateY(-3px) scale(1.02);
    opacity: 0.95;
}

.mcd { background: #ef4444; }
.bk { background: #f97316; }
.kfc { background: #dc2626; }
.otacos { background: #0f172a; }

.stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
    margin-top: 20px;
}

.stat-row {
    background: #f8fafc;
    border-radius: 14px;
    padding: 14px;
    transition: transform 0.18s ease, background 0.18s ease;
}

.stat-row:hover {
    transform: translateY(-2px);
    background: #f1f5f9;
}

.stat-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.restaurant-name {
    font-weight: bold;
    font-size: 17px;
}

.vote-count {
    color: #334155;
    font-size: 14px;
}

.bar {
    width: 100%;
    height: 22px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    border-radius: 999px;
    color: white;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    transition: width 0.4s ease;
}

.bar-mcd { background: #ef4444; }
.bar-bk { background: #f97316; }
.bar-kfc { background: #dc2626; }
.bar-otacos { background: #0f172a; }

.winner-box {
    margin-top: 16px;
    padding: 16px;
    border-radius: 14px;
    background: #fef3c7;
    color: #92400e;
    font-weight: bold;
}

.footer-link {
    display: inline-block;
    margin-top: 18px;
    text-decoration: none;
    color: #2563eb;
    font-weight: bold;
}

.footer-link:hover {
    text-decoration: underline;
}

.small-note {
    color: #64748b;
    font-size: 14px;
    margin-top: 14px;
}
</style>
</head>
<body>
<div class="container">

<div class="card">
<h1>Vote pour ton fast-food préféré</h1>
<p class="subtitle">Choisis ton champion, puis consulte les résultats en direct.</p>

<?php if ($message !== ''): ?>
<div class="message <?php echo htmlspecialchars($messageType); ?>">
<?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<form action="vote.php" method="POST">
<div class="vote-grid">
<button class="vote-btn mcd" type="submit" name="restaurant" value="McDonalds">🍔 McDonald's</button>
<button class="vote-btn bk" type="submit" name="restaurant" value="BurgerKing">👑 Burger King</button>
<button class="vote-btn kfc" type="submit" name="restaurant" value="KFC">🍗 KFC</button>
<button class="vote-btn otacos" type="submit" name="restaurant" value="OTacos">🌯 O'Tacos</button>
</div>
</form>

<p class="small-note">
Un cookie simple empêche de revoter pendant 1 heure.
</p>
</div>

<div class="card">
<h2>Résultats</h2>
<p class="subtitle">Total des votes : <strong><?php echo $totalVotes; ?></strong></p>

<div class="stats">
<?php foreach ($restaurants as $key => $label): ?>
<?php
$count = $results[$key];
$percent = $totalVotes > 0 ? round(($count / $totalVotes) * 100, 1) : 0;

$barClass = '';
if ($key === 'McDonalds') $barClass = 'bar-mcd';
if ($key === 'BurgerKing') $barClass = 'bar-bk';
if ($key === 'KFC') $barClass = 'bar-kfc';
if ($key === 'OTacos') $barClass = 'bar-otacos';
?>
<div class="stat-row">
<div class="stat-top">
<div class="restaurant-name"><?php echo htmlspecialchars($label); ?></div>
<div class="vote-count"><?php echo $count; ?> vote(s) — <?php echo $percent; ?>%</div>
</div>
<div class="bar">
<div class="bar-fill <?php echo $barClass; ?>" style="width: <?php echo $percent; ?>%;">
<?php echo $percent; ?>%
</div>
</div>
</div>
<?php endforeach; ?>
</div>

<?php if ($winnerKey !== null && $winnerVotes > 0): ?>
<div class="winner-box">
🏆 En tête : <?php echo htmlspecialchars($restaurants[$winnerKey]); ?> avec <?php echo $winnerVotes; ?> vote(s)
</div>
<?php endif; ?>

<a class="footer-link" href="results.php">Voir la page de résultats détaillés</a>
</div>

</div>
</body>
</html>
