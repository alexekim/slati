<?php
require_once("dompdf_config.inc.php");
$html =
    '<html><body>'.
    '<p>Hello World!</p>'.
    '</body></html>';

$dompdf = new DOMPDF();
$dompdf->load_html($html);

$dompdf->render();
$dompdf->stream("hello_world.pdf");

?>
