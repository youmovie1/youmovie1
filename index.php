<?php

// index.php
// ... (if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $m3u_url = $_POST['m3u_url'];

    // Descargar y leer el archivo M3U
    $m3u_content = @file_get_contents($m3u_url);

    $videoEntries = []; // Arreglo para almacenar las entradas de video

    if ($m3u_content === false) {
        $error = "Error al descargar el archivo M3U. Verifica la URL.";
    } else {
        $lines = explode("\n", $m3u_content);
        $videoName = '';
        $videoUrl = '';
        $videoLogo = 'https://cdn-icons-png.flaticon.com/512/1160/1160358.png'; // Imagen predeterminada

        foreach ($lines as $line) {
            $line = trim($line);

            if (strpos($line, '#EXTINF') === 0) {
                // Extraer el nombre del video
                $parts = explode(',', $line);
                $videoName = isset($parts[1]) ? $parts[1] : '';

                // Buscar si hay una imagen logo en el formato logo="url"
                preg_match('/logo="(.*?)"/', $line, $logoMatch);
                if (isset($logoMatch[1])) {
                    $videoLogo = $logoMatch[1];
                }
            } elseif ($line && strpos($line, '#') !== 0) {
                // Asignar la URL del video
                $videoUrl = $line;

                // Agregar la entrada al arreglo
                $videoEntries[] = [
                    'url' => $videoUrl,
                    'name' => $videoName,
                    'logo' => $videoLogo
                ];

                // Reiniciar el logo a la imagen predeterminada
                $videoLogo = 'https://cdn-icons-png.flaticon.com/512/1160/1160358.png';
            }
        }
    }
})

include 'template.html';
?>
