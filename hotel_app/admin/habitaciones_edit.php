<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM habitaciones WHERE id = :id');
$stmt->execute(['id'=>$id]);
$room = $stmt->fetch();
if (!$room) { echo '<div class="alert alert-danger">No encontrada</div>'; include __DIR__ . '/../includes/footer.php'; exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = trim($_POST['numero']);
    $tipo = $_POST['tipo'];
    $precio = $_POST['precio'];
    $estado_limpieza = $_POST['estado_limpieza'];
  // manejo de imagen opcional
  $imagen_nombre = null;
  if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['imagen']['tmp_name'];
    $orig = basename($_FILES['imagen']['name']);
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (in_array($ext, $allowed)) {
      $imagen_nombre = uniqid('rm_') . '.' . $ext;
      $dest = __DIR__ . '/../assets/uploads/' . $imagen_nombre;
      if (!move_uploaded_file($tmp, $dest)) {
        $err = 'No se pudo mover la imagen subida.';
        $imagen_nombre = null;
      }
    } else {
      $err = 'Tipo de imagen no permitido.';
    }
  }

  try {
    if ($imagen_nombre) {
      $stmt = $pdo->prepare('UPDATE habitaciones SET numero=:n, tipo=:t, precio_base=:p, estado_limpieza=:e, imagen=:img WHERE id=:id');
      $stmt->execute(['n'=>$numero,'t'=>$tipo,'p'=>$precio,'e'=>$estado_limpieza,'img'=>$imagen_nombre,'id'=>$id]);
    } else {
      $stmt = $pdo->prepare('UPDATE habitaciones SET numero=:n, tipo=:t, precio_base=:p, estado_limpieza=:e WHERE id=:id');
      $stmt->execute(['n'=>$numero,'t'=>$tipo,'p'=>$precio,'e'=>$estado_limpieza,'id'=>$id]);
    }
    header('Location: habitaciones.php'); exit;
  } catch (Exception $ex) {
    $err = 'Error al actualizar: ' . $ex->getMessage();
  }
}
?>
<h2>Editar habitación <?= e($room['numero']) ?></h2>
<?php if($err) echo '<div class="alert alert-danger">'.e($err).'</div>'; ?>
<form method="post">
  <?php if(!empty($room['imagen'])): ?>
    <div class="mb-3">
      <label>Imagen actual</label>
      <div><img src="/hotel_app/assets/uploads/<?= e($room['imagen']) ?>" alt="thumb" style="max-width:150px;border-radius:6px;"></div>
    </div>
  <?php endif; ?>
  <div class="mb-3"><label>Subir nueva imagen (opcional)</label><input class="form-control" name="imagen" type="file" accept="image/*"></div>
  <div class="mb-3"><label>Número</label><input class="form-control" name="numero" value="<?= e($room['numero']) ?>" required></div>
  <div class="mb-3"><label>Tipo</label>
    <select class="form-select" name="tipo">
      <option <?= $room['tipo']=='Sencilla'?'selected':'' ?>>Sencilla</option>
      <option <?= $room['tipo']=='Doble'?'selected':'' ?>>Doble</option>
      <option <?= $room['tipo']=='Suite'?'selected':'' ?>>Suite</option>
    </select>
  </div>
  <div class="mb-3"><label>Precio base</label><input class="form-control" name="precio" type="number" step="0.01" value="<?= e($room['precio_base']) ?>" required></div>
  <div class="mb-3"><label>Estado limpieza</label>
    <select class="form-select" name="estado_limpieza">
      <option <?= $room['estado_limpieza']=='Limpia'?'selected':'' ?>>Limpia</option>
      <option <?= $room['estado_limpieza']=='Sucia'?'selected':'' ?>>Sucia</option>
      <option <?= $room['estado_limpieza']=='En Limpieza'?'selected':'' ?>>En Limpieza</option>
    </select>
  </div>
  <button class="btn btn-primary">Actualizar</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>