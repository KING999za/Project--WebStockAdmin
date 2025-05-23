<?php
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

$fontpath = __DIR__ . '/fonts/THSarabunNew.ttf';

if (!file_exists($fontpath)) {
    echo "ERROR: Font file not found at $fontpath\n";
    exit;
}

$fontfile = TCPDF_FONTS::addTTFfont($fontpath, 'TrueTypeUnicode', '', 32);

if ($fontfile) {
    echo "Font added: $fontfile\n";
} else {
    echo "ERROR: Failed to add font.\n";
}

// ถ้าแปลง Bold ด้วย (ถ้ามี)
$fontpath_bold = __DIR__ . '/fonts/THSarabunNewBold.ttf';
if (file_exists($fontpath_bold)) {
    $fontfile_bold = TCPDF_FONTS::addTTFfont($fontpath_bold, 'TrueTypeUnicode', '', 32);
    if ($fontfile_bold) {
        echo "Bold font added: $fontfile_bold\n";
    } else {
        echo "ERROR: Failed to add bold font.\n";
    }
} else {
    echo "Bold font file not found: $fontpath_bold\n";
}
?>
