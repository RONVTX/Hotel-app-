<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';

// delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare('DELETE FROM habitaciones WHERE id = :id');
    $stmt->execute(['id' => $id]);
    header('Location: habitaciones.php'); exit;
}
$rooms = $pdo->query('SELECT * FROM habitaciones ORDER BY id DESC')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Habitaciones</h2>
  <a class="btn btn-success" href="habitaciones_add.php">Agregar habitación</a>
</div>
<table class="table table-striped">
  <thead><tr><th>#</th><th>Imagen</th><th>Namero</th><th>Tipo</th><th>Precio</th><th>Estado limpieza</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($rooms as $r): ?>
      <tr>
        <td><?= e($r['id']) ?></td>
        <td>
          <?php if(!empty($r['imagen'])): ?>
            <img src="/hotel_app/assets/uploads/<?= e($r['imagen']) ?>" alt="thumb" style="width:60px;height:40px;object-fit:cover;border-radius:4px;">
          <?php else: ?>
            <div style="width:60px;height:40px;background:#f1f1f1;border-radius:4px;display:inline-block;"></div>
          <?php endif; ?>
        </td>
        <td><?= e($r['numero']) ?></td>
        <td><?= e($r['tipo']) ?></td>
        <td><?= e($r['precio_base']) ?></td>
        <td><?= e($r['estado_limpieza']) ?></td>
        <td>
          <a class="btn btn-sm btn-primary" href="habitaciones_edit.php?id=<?= e($r['id']) ?>">Editar</a>
          <a class="btn btn-sm btn-danger" href="?delete=<?= e($r['id']) ?>" onclick="return confirm('Eliminar?')">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>