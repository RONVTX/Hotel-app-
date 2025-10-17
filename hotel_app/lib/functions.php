<?php
// lib/functions.php
require_once __DIR__ . '/../config/db.php';

function fechas_solapan($start1, $end1, $start2, $end2) {
    $s1 = new DateTime($start1); $e1 = new DateTime($end1);
    $s2 = new DateTime($start2); $e2 = new DateTime($end2);
    return ($s1 <= $e2) && ($s2 <= $e1);
}

function habitacion_disponible($habitacion_id, $fecha_llegada, $fecha_salida) {
    global $pdo;
    // reservas confirmadas
    $sql = "SELECT fecha_llegada, fecha_salida FROM reservas
            WHERE habitacion_id = :hid AND estado = 'Confirmada'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hid' => $habitacion_id]);
    $reservas = $stmt->fetchAll();
    foreach ($reservas as $r) {
        if (fechas_solapan($r['fecha_llegada'], $r['fecha_salida'], $fecha_llegada, $fecha_salida)) {
            return ['ok' => false, 'reason' => 'Existe una reserva confirmada que se solapa.'];
        }
    }

    // mantenimiento activo (estado <> 'Cerrada')
    $sql = "SELECT fecha_inicio, fecha_fin FROM mantenimiento
            WHERE habitacion_id = :hid AND estado <> 'Cerrada'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hid' => $habitacion_id]);
    $tareas = $stmt->fetchAll();
    foreach ($tareas as $t) {
        if (fechas_solapan($t['fecha_inicio'], $t['fecha_fin'], $fecha_llegada, $fecha_salida)) {
            return ['ok' => false, 'reason' => 'La habitación tiene tareas de mantenimiento activas en esas fechas.'];
        }
    }

    // bloqueo por estado de limpieza "En Limpieza" durante todo el rango opcional
    $sql = "SELECT estado_limpieza FROM habitaciones WHERE id = :hid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hid' => $habitacion_id]);
    $h = $stmt->fetch();
    if ($h && $h['estado_limpieza'] === 'En Limpieza') {
        return ['ok' => false, 'reason' => 'La habitación está en limpieza.'];
    }

    return ['ok' => true];
}

function calcular_noches($fecha_llegada, $fecha_salida) {
    $d1 = new DateTime($fecha_llegada);
    $d2 = new DateTime($fecha_salida);
    $interval = $d1->diff($d2);
    return (int)$interval->days;
}

function crear_huesped_si_no_existe($nombre, $email, $documento) {
    global $pdo;
    $sql = "SELECT id FROM huespedes WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $r = $stmt->fetch();
    if ($r) return $r['id'];
    $sql = "INSERT INTO huespedes (nombre, email, documento_identidad) VALUES (:n, :e, :d)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['n' => $nombre, 'e' => $email, 'd' => $documento]);
    return $pdo->lastInsertId();
}

function crear_reserva($huesped_id, $habitacion_id, $fecha_llegada, $fecha_salida, $estado = 'Pendiente') {
    global $pdo;
    if (new DateTime($fecha_salida) <= new DateTime($fecha_llegada)) {
        return ['ok' => false, 'reason' => 'Fecha de salida debe ser posterior a llegada.'];
    }
    $dispo = habitacion_disponible($habitacion_id, $fecha_llegada, $fecha_salida);
    if (!$dispo['ok']) return $dispo;

    $sql = "SELECT precio_base FROM habitaciones WHERE id = :hid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hid' => $habitacion_id]);
    $row = $stmt->fetch();
    if (!$row) return ['ok' => false, 'reason' => 'Habitación no encontrada.'];

    $noches = calcular_noches($fecha_llegada, $fecha_salida);
    if ($noches <= 0) return ['ok' => false, 'reason' => 'Rango de fechas inválido.'];

    $precio_total = bcmul((string)$row['precio_base'], (string)$noches, 2);

    $sql = "INSERT INTO reservas (huesped_id, habitacion_id, fecha_llegada, fecha_salida, precio_total, estado)
            VALUES (:hidp, :habitacion, :f_lleg, :f_sal, :precio, :estado)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'hidp' => $huesped_id,
        'habitacion' => $habitacion_id,
        'f_lleg' => $fecha_llegada,
        'f_sal' => $fecha_salida,
        'precio' => $precio_total,
        'estado' => $estado
    ]);
    return ['ok' => true, 'reserva_id' => $pdo->lastInsertId(), 'precio_total' => $precio_total];
}
