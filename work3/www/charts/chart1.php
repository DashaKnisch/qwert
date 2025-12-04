<?php
require __DIR__ . '/../vendor/autoload.php';
require 'chart_base.php';

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\BarPlot;

$mysqli = new mysqli("db", "appuser", "apppass", "appdb");
$mysqli->set_charset("utf8mb4");

$res = $mysqli->query("SELECT month, AVG(sales) AS avg_sales FROM stats_data GROUP BY month");

$monthOrder = ["January","February","March","April","May","June","July","August","September","October","November","December"];
$data = [];
$labels = [];

while($row = $res->fetch_assoc()){
    $data[$row['month']] = round($row['avg_sales']);
}

foreach($monthOrder as $m){
    $values[] = $data[$m] ?? 0;
    $labels[] = $m;
}

$graph = new Graph(800, 400);
$graph->SetScale('textlin');
$graph->img->SetMargin(60, 20, 40, 60);
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->title->Set("Month");
$graph->yaxis->title->Set("Average Sales");

$barplot = new BarPlot($values);
$barplot->SetFillColor("blue");
$barplot->value->Show();
$barplot->value->SetFormat('%d');
$graph->Add($barplot);

$gdImg = $graph->Stroke(_IMG_HANDLER);

add_watermark($gdImg, "DashaK");

header("Content-Type: image/png");
imagepng($gdImg);
imagedestroy($gdImg);
