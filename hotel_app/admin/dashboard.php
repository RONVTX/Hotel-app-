<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';

$habitaciones_count = $pdo->query("SELECT COUNT(*) as c FROM habitaciones")->fetch()['c'];
$huespedes_count = $pdo->query("SELECT COUNT(*) as c FROM huespedes")->fetch()['c'];
$reservas_count = $pdo->query("SELECT COUNT(*) as c FROM reservas")->fetch()['c'];
?>
<h1>Panel de control</h1>
<div class="row mt-4">
  <div class="col-md-4"><div class="card p-3">Habitaciones: <strong><?= e($habitaciones_count) ?></strong></div></div>
  <div class="col-md-4"><div class="card p-3">Huéspedes: <strong><?= e($huespedes_count) ?></strong></div></div>
  <div class="col-md-4"><div class="card p-3">Reservas: <strong><?= e($reservas_count) ?></strong></div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>