<?php
require __DIR__ . '/../vendor/autoload.php';
require 'chart_base.php';

use Amenadiel\JpGraph\Graph\PieGraph;
use Amenadiel\JpGraph\Plot\PiePlot;

$mysqli = new mysqli("db", "appuser", "apppass", "appdb");
$mysqli->set_charset("utf8mb4");

$res = $mysqli->query("SELECT category, AVG(rating) AS avg_rating FROM stats_data GROUP BY category");
$data = [];
$labels = [];
while($row = $res->fetch_assoc()){
    $data[] = round($row['avg_rating']);
    $labels[] = $row['category'];
}

$graph = new PieGraph(500, 400);
$graph->SetShadow();

$pieplot = new PiePlot($data);
$pieplot->SetLegends($labels);
$pieplot->SetColor("white");
$pieplot->ShowBorder(true);

$graph->Add($pieplot);

$gdImg = $graph->Stroke(_IMG_HANDLER);

add_watermark($gdImg, "DashaK");

header("Content-Type: image/png");
imagepng($gdImg);
imagedestroy($gdImg);
