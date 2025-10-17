<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';

if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $stmt = $pdo->prepare('UPDATE reservas SET estado = "Cancelada" WHERE id = :id');
    $stmt->execute(['id'=>$id]);
    header('Location: reservas.php'); exit;
}

$sql = "SELECT r.*, h.numero AS numero, hh.nombre AS huesped_nombre
        FROM reservas r
        JOIN habitaciones h ON h.id = r.habitacion_id
        JOIN huespedes hh ON hh.id = r.huesped_id
        ORDER BY r.created_at DESC";
$res = $pdo->query($sql)->fetchAll();
?>
<h2>Reservas</h2>
<table class="table">
  <thead><tr><th>#</th><th>Huésped</th><th>Hab</th><th>Llegada</th><th>Salida</th><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($res as $r): ?>
      <tr>
        <td><?= e($r['id']) ?></td>
        <td><?= e($r['huesped_nombre']) ?></td>
        <td><?= e($r['numero']) ?></td>
        <td><?= e($r['fecha_llegada']) ?></td>
        <td><?= e($r['fecha_salida']) ?></td>
        <td><?= e($r['precio_total']) ?></td>
        <td><?= e($r['estado']) ?></td>
        <td>
          <?php if($r['estado']!='Cancelada'): ?>
            <a class="btn btn-sm btn-danger" href="?cancel=<?= e($r['id']) ?>" onclick="return confirm('Cancelar reserva?')">Cancelar</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>