<?php
// Función para detectar si el visitante es una araña de buscador
function es_arana_busqueda($user_agent) {
    $aranas = ['Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot', 'Baiduspider', 'YandexBot', 'Sogou'];
    foreach ($aranas as $arana) {
        if (stripos($user_agent, $arana) !== false) {
            return true;
        }
    }
    return false;
}

// Obtener la IP del usuario y el User Agent
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Llamar a la API de ipinfo para obtener información de la IP
$ipinfo = @file_get_contents("http://ipinfo.io/{$ip_address}/json");
if ($ipinfo === FALSE) {
    die('No se pudo obtener información de la IP.');
}
$ipinfo_data = json_decode($ipinfo, true);

// Obtener el país (puedes mostrar otro contenido dependiendo del país)
$pais = $ipinfo_data['country'] ?? 'Desconocido';

// Bloquear acceso si el país es específico (por ejemplo, bloquear acceso desde China)
if ($pais === 'CN') {
    die('Acceso bloqueado desde China');
}

// Registro de la petición (IP, User Agent y fecha)
$log_entry = date("Y-m-d H:i:s") . " - IP: $ip_address - User Agent: $user_agent\n";
file_put_contents('logs.txt', $log_entry, FILE_APPEND);

// Comportamiento especial para arañas de buscadores
if (es_arana_busqueda($user_agent)) {
    die('Contenido restringido para arañas de buscadores.');
}

// Detectar si es móvil o escritorio según el User Agent
$es_movil = preg_match('/Mobile|Android|iPhone|iPad|Opera Mini|IEMobile|WPDesktop/', $user_agent);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido según país</title>
</head>
<body>
    <h1>Contenido Personalizado</h1>
    <p><strong>Tu dirección IP es:</strong> <?php echo htmlspecialchars($ip_address); ?></p>
    <p><strong>Tu país es:</strong> <?php echo htmlspecialchars($pais); ?></p>

    <?php if ($pais === 'MX'): ?>
        <p>Bienvenido, usuario de México.</p>
    <?php else: ?>
        <p>Bienvenido desde otro país.</p>
    <?php endif; ?>

    <?php if ($es_movil): ?>
        <p>Estás navegando desde un dispositivo móvil.</p>
    <?php else: ?>
        <p>Descargando archivo automáticamente...</p>
        <script>
            window.location.href = 'robot.txt'; // Reemplaza con el archivo que quieras descargar
        </script>
    <?php endif; ?>
</body>
</html>
