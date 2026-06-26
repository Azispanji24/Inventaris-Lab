<?php
$assetBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$assetBase = $assetBase === '' || $assetBase === '.' ? '' : $assetBase;
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?php echo $assetBase; ?>/assets/css/style.css?v=2026062602" rel="stylesheet">
