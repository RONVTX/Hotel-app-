<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare('DELETE FROM huespedes WHERE id = :id');
    $stmt->execute(['id' => $id]);
    header('Location: huespedes.php'); exit;
}
$guests = $pdo->query('SELECT * FROM huespedes ORDER BY id DESC')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Huéspedes</h2>
  <a class="btn btn-success" href="huespedes_add.php">Agregar huésped</a>
</div>
<table class="table">
  <thead><tr><th>#</th><th>Nombre</th><th>Email</th><th>Documento</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($guests as $g): ?>
    <tr>
      <td><?= e($g['id']) ?></td>
      <td><?= e($g['nombre']) ?></td>
      <td><?= e($g['email']) ?></td>
      <td><?= e($g['documento_identidad']) ?></td>
      <td>
        <a class="btn btn-sm btn-danger" href="?delete=<?= e($g['id']) ?>" onclick="return confirm('Eliminar?')">Eliminar</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>