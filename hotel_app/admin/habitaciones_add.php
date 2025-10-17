<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $numero = trim($_POST['numero']);
  $tipo = $_POST['tipo'];
  $precio = $_POST['precio'];
  $estado_limpieza = $_POST['estado_limpieza'];

  // manejo de imagen (opcional)
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

  // insertar (imagen columna opcional)
  try {
    if ($imagen_nombre) {
      $stmt = $pdo->prepare('INSERT INTO habitaciones (numero, tipo, precio_base, estado_limpieza, imagen) VALUES (:n, :t, :p, :e, :img)');
      $stmt->execute(['n'=>$numero,'t'=>$tipo,'p'=>$precio,'e'=>$estado_limpieza,'img'=>$imagen_nombre]);
    } else {
      $stmt = $pdo->prepare('INSERT INTO habitaciones (numero, tipo, precio_base, estado_limpieza) VALUES (:n, :t, :p, :e)');
      $stmt->execute(['n'=>$numero,'t'=>$tipo,'p'=>$precio,'e'=>$estado_limpieza]);
    }
    header('Location: habitaciones.php'); exit;
  } catch (Exception $ex) {
    $err = 'Error al guardar: ' . $ex->getMessage();
  }
}
?>
<h2>Agregar habitación</h2>
<?php if($err) echo '<div class="alert alert-danger">'.e($err).'</div>'; ?>
<form method="post">
  <div class="mb-3"><label>Número</label><input class="form-control" name="numero" required></div>
  <div class="mb-3"><label>Tipo</label>
    <select class="form-select" name="tipo">
      <option>Sencilla</option><option>Doble</option><option>Suite</option>
    </select>
  </div>
  <div class="mb-3"><label>Precio base</label><input class="form-control" name="precio" type="number" step="0.01" required></div>
  <div class="mb-3"><label>Estado limpieza</label>
    <select class="form-select" name="estado_limpieza">
      <option>Limpia</option><option>Sucia</option><option>En Limpieza</option>
    </select>
  </div>
  <button class="btn btn-success">Guardar</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>