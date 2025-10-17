<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';
include __DIR__ . '/../includes/header.php';

$habitacion_id = $_GET['habitacion_id'] ?? '';
$habitacion = null;
if ($habitacion_id) {
    $stmt = $pdo->prepare('SELECT * FROM habitaciones WHERE id = :id');
    $stmt->execute(['id' => $habitacion_id]);
    $habitacion = $stmt->fetch();
}

$errors = [];
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $documento = trim($_POST['documento']);
    $habitacion_id = intval($_POST['habitacion_id']);
    $fecha_llegada = $_POST['fecha_llegada'];
    $fecha_salida = $_POST['fecha_salida'];

    if (!$nombre || !$email || !$documento) $errors[] = 'Rellena los datos del huésped.';
    if (!$fecha_llegada || !$fecha_salida) $errors[] = 'Rellena las fechas.';

    if (empty($errors)) {
        $huesped_id = crear_huesped_si_no_existe($nombre, $email, $documento);
        $res = crear_reserva($huesped_id, $habitacion_id, $fecha_llegada, $fecha_salida, 'Pendiente');
        if ($res['ok']) {
            $success = 'Reserva creada correctamente. Total: ' . $res['precio_total'] . ' € (ID: ' . $res['reserva_id'] . ')';
        } else {
            $errors[] = $res['reason'];
        }
    }
}
?>
<h2>Reservar habitación</h2>

<?php if($errors): ?>
  <div class="alert alert-danger">
    <?php foreach($errors as $e) echo "<div>" . e($e) . "</div>"; ?>
  </div>
<?php endif; ?>

<?php if($success): ?>
  <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<form method="post">
  <input type="hidden" name="habitacion_id" value="<?= e($habitacion_id) ?>">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input class="form-control" name="nombre" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input class="form-control" name="email" type="email" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Documento identidad</label>
    <input class="form-control" name="documento" required>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Fecha llegada</label>
      <input type="date" class="form-control" name="fecha_llegada" required>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Fecha salida</label>
      <input type="date" class="form-control" name="fecha_salida" required>
    </div>
  </div>
  <button class="btn btn-primary">Reservar</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>