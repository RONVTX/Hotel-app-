<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $habitacion_id = intval($_POST['habitacion_id']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $stmt = $pdo->prepare('INSERT INTO mantenimiento (habitacion_id, descripcion, fecha_inicio, fecha_fin) VALUES (:hid,:d,:fi,:ff)');
    try {
        $stmt->execute(['hid'=>$habitacion_id,'d'=>$descripcion,'fi'=>$fecha_inicio,'ff'=>$fecha_fin]);
        header('Location: mantenimiento.php'); exit;
    } catch (Exception $ex) { $err = $ex->getMessage(); }
}
if (isset($_GET['close'])) {
    $id = intval($_GET['close']);
    $stmt = $pdo->prepare('UPDATE mantenimiento SET estado = "Cerrada" WHERE id = :id');
    $stmt->execute(['id'=>$id]);
    header('Location: mantenimiento.php'); exit;
}

$tasks = $pdo->query('SELECT m.*, h.numero FROM mantenimiento m JOIN habitaciones h ON h.id = m.habitacion_id ORDER BY m.created_at DESC')->fetchAll();
$rooms = $pdo->query('SELECT * FROM habitaciones')->fetchAll();
?>
<h2>Mantenimiento</h2>
<?php if($err) echo '<div class="alert alert-danger">'.e($err).'</div>'; ?>
<form method="post" class="mb-4">
  <input type="hidden" name="add_task" value="1">
  <div class="row">
    <div class="col-md-3"><label>Habitación</label>
      <select name="habitacion_id" class="form-select"><?php foreach($rooms as $r) echo '<option value="'.e($r['id']).'">'.e($r['numero']).'</option>'; ?></select>
    </div>
    <div class="col-md-3"><label>Fecha inicio</label><input type="date" name="fecha_inicio" class="form-control" required></div>
    <div class="col-md-3"><label>Fecha fin</label><input type="date" name="fecha_fin" class="form-control" required></div>
    <div class="col-md-3"><label>Descripción</label><input class="form-control" name="descripcion" required></div>
  </div>
  <button class="btn btn-primary mt-2">Añadir tarea</button>
</form>

<table class="table">
  <thead><tr><th>#</th><th>Hab</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($tasks as $t): ?>
    <tr>
      <td><?= e($t['id']) ?></td>
      <td><?= e($t['numero']) ?></td>
      <td><?= e($t['fecha_inicio']) ?></td>
      <td><?= e($t['fecha_fin']) ?></td>
      <td><?= e($t['estado']) ?></td>
      <td>
        <?php if($t['estado'] != 'Cerrada'): ?>
          <a class="btn btn-sm btn-success" href="?close=<?= e($t['id']) ?>">Cerrar</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>