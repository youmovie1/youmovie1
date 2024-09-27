<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reproductor M3U con Imágenes desde URL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }
        input {
            display: block;
            margin-bottom: 20px;
            width: 100%;
            padding: 10px;
        }
        .thumbnail {
            display: inline-block;
            position: relative;
            margin: 10px;
            width: 200px;
            cursor: pointer;
        }
        .thumbnail img {
            width: 100%;
            height: auto;
            display: block;
        }
        .thumbnail .diagonal-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background-color: rgba(0, 0, 0, 0.5);
            pointer-events: none;
        }
        .thumbnail div[data-title] {
            position: absolute;
            bottom: 10px;
            left: 10px;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Reproductor de Listas M3U con Imágenes desde URL</h1>

<form method="post" action="">
    <input type="text" name="m3u_url" placeholder="Ingresa la URL del archivo .m3u" required>
    <button type="submit">Cargar M3U</button>
</form>

<div id="videoList">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $m3u_url = $_POST['m3u_url'];

        // Descargar y leer el archivo M3U
        $m3u_content = @file_get_contents($m3u_url);

        if ($m3u_content === false) {
            echo "<p>Error al descargar el archivo M3U. Verifica la URL.</p>";
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

                    // Mostrar la entrada
                    echo '<div class="thumbnail" data-genres="Aventura" onclick="openFullscreenVideo(\'' . $videoUrl . '\')" data-title="' . $videoName . '">';
                    echo '<img src="' . $videoLogo . '" alt="' . htmlspecialchars($videoName) . '">';
                    echo '<div class="diagonal-bar"></div>';
                    echo '<div data-title="' . htmlspecialchars($videoName) . '">' . htmlspecialchars($videoName) . '</div>';
                    echo '</div>';

                    // Reiniciar el logo a la imagen predeterminada
                    $videoLogo = 'https://cdn-icons-png.flaticon.com/512/1160/1160358.png';
                }
            }
        }
    }
    ?>
</div>

<script>
    function openFullscreenVideo(fullUrl) {
        const videoPlayer = document.createElement('video');
        videoPlayer.src = fullUrl;
        videoPlayer.style.position = 'absolute';
        videoPlayer.style.top = '0';
        videoPlayer.style.left = '0';
        videoPlayer.style.width = '100%';
        videoPlayer.style.height = '100%';
        videoPlayer.style.border = 'none';
        videoPlayer.controls = true;
        videoPlayer.autoplay = true;

        document.body.appendChild(videoPlayer);

        // Manejo de pantalla completa
        if (videoPlayer.requestFullscreen) {
            videoPlayer.requestFullscreen();
        } else if (videoPlayer.mozRequestFullScreen) {
            videoPlayer.mozRequestFullScreen();
        } else if (videoPlayer.webkitRequestFullscreen) {
            videoPlayer.webkitRequestFullscreen();
        } else if (videoPlayer.msRequestFullscreen) {
            videoPlayer.msRequestFullscreen();
        }

        document.addEventListener('fullscreenchange', exitHandler);
        document.addEventListener('webkitfullscreenchange', exitHandler);

        function exitHandler() {
            if (!document.fullscreenElement && !document.webkitIsFullScreen) {
                document.body.removeChild(videoPlayer);
                document.removeEventListener('fullscreenchange', exitHandler);
                document.removeEventListener('webkitfullscreenchange', exitHandler);
            }
        }

        videoPlayer.addEventListener('contextmenu', (event) => event.preventDefault());
    }
</script>

</body>
</html>
