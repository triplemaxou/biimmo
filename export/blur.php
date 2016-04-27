    <?php
    // Tiré de http://www.gamedev.net/reference/programming/features/imageproc/page2.asp
    $coeffs = array (
                                    array ( 1),
                                    array ( 1, 1),
                                    array ( 1, 2, 1),
                                    array ( 1, 3, 3, 1),
                                    array ( 1, 4, 6, 4, 1),
                                    array ( 1, 5, 10, 10, 5, 1),
                                    array ( 1, 6, 15, 20, 15, 6, 1),
                                    array ( 1, 7, 21, 35, 35, 21, 7, 1),
                                    array ( 1, 8, 28, 56, 70, 56, 28, 8, 1),
                                    array ( 1, 9, 36, 84, 126, 126, 84, 36, 9, 1),
                                    array ( 1, 10, 45, 120, 210, 252, 210, 120, 45, 10, 1),
                                    array ( 1, 11, 55, 165, 330, 462, 462, 330, 165, 55, 11, 1)
                                    );
    if (!isset($_GET['img'])) die('Pas d\'image');
    if (!isset($_GET['index'])) die('Pas de filtre');
    if ($_GET['index']<0 || $_GET['index']>11) die ('index 0..11 only !');
    if (strstr('http://', $_GET['img'])) die('Only for internal use');
    $src = array();
    $src['index'] = $_GET['index'];
    $src['sum'] = pow (2, $src['index']);
    // On vérifie l'existence de l'image
    $src['file'] = $_GET['img'];
    if (!file_exists($src['file'])) die('Image does not exist');
    // On cherche l'image en cache
    $src['cache_file'] = "./cache/blur".md5($src['file'].$src['index']);
    if (file_exists($src['cache_file']) &&
        (intval(filemtime($src['file'])) < intval(filemtime($src['cache_file']))) &&
        filesize($src['cache_file'])) {
        // Si on a l'image traitée en cache
        // Et que l'image source n'est pas plus récente que celle en cache
        // on l'envoie !
            header ("Content-Type: image/png");
        readfile($src['cache_file']);
        exit();
    }
    // Sinon on continue le traitement
    touch($src['cache_file']);
    // On se procure les infos sur l'image (taille/type mime)
    $src['infos'] = getimagesize($src['file']);
    // On charge l'image de départ en fonction de son type
    switch($src['infos']['mime']) {
        case 'image/jpg':
        case 'image/jpeg':
            $src['img'] = imagecreatefromjpeg($src['file']);
            break;
        case 'image/gif':
            $src['img'] = imagecreatefromgif($src['file']);
            break;
        case 'image/png':
            $src['img'] = imagecreatefrompng($src['file']);
            break;
        default:
            exit();
    }
    $src['temp1'] = imagecreatetruecolor ($src['infos'][0], $src['infos'][1]);
    $src['temp2'] = imagecreatetruecolor ($src['infos'][0], $src['infos'][1]);
    // Traitement
    for ( $i=0 ; $i < $src['infos'][0] ; ++$i ) {
            for ( $j=0 ; $j < $src['infos'][1] ; ++$j ) {
                    $sumr=0;
                    $sumg=0;
                    $sumb=0;
                    for ( $k=0 ; $k <= $src['index'] ; ++$k ) {
                            $color = imagecolorat($src['img'], $i-(($src['index'])/2)+$k, $j);
                            $r = ($color >> 16) & 0xFF;
                            $g = ($color >> 8) & 0xFF;
                            $b = ($color) & 0xFF;
                            $sumr += $r*$coeffs[$src['index']][$k];
                            $sumg += $g*$coeffs[$src['index']][$k];
                            $sumb += $b*$coeffs[$src['index']][$k];
                    }
                    $color = imagecolorallocate ($src['temp1'], $sumr/$src['sum'], $sumg/$src['sum'], $sumb/$src['sum'] );
                    imagesetpixel($src['temp1'],$i,$j,$color);
            }
    }
    imagedestroy($src['img']);
    for ( $i=0 ; $i < $src['infos'][0] ; ++$i ) {
            for ( $j=0 ; $j < $src['infos'][1] ; ++$j ) {
                    $sumr=0;
                    $sumg=0;
                    $sumb=0;
                    for ( $k=0 ; $k <= $src['index'] ; ++$k ) {
                            $color=imagecolorat($src['temp1'], $i, $j-(($src['index'])/2)+$k);
                            $r=($color >> 16) & 0xFF;
                            $g=($color >> 8) & 0xFF;
                            $b=($color) & 0xFF;
                            $sumr += $r*$coeffs[$src['index']][$k];
                            $sumg += $g*$coeffs[$src['index']][$k];
                            $sumb += $b*$coeffs[$src['index']][$k];
                    }
                    $color = imagecolorallocate ($src['temp2'], $sumr/$src['sum'], $sumg/$src['sum'], $sumb/$src['sum'] );
                    imagesetpixel($src['temp2'],$i,$j,$color);
            }
    }
    imagedestroy($src['temp1']);
    header("Content-Type: image/png");
    imagepng($src['temp2'], $src['cache_file']);
    imagedestroy($src['temp2']);
    readfile($src['cache_file']);
    ?>
