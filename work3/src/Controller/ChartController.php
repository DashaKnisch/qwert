<?php

namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ChartController extends AbstractController 
{
    public function chart1(): Response 
    {
        // Определяем пути для Docker и локального окружения
        $isDocker = file_exists('/var/www/www/vendor/autoload.php');
        $vendorPath = $isDocker ? '/var/www/www/vendor/autoload.php' : __DIR__ . '/../../www/vendor/autoload.php';
        
        // Используем существующую логику из www/charts/chart1.php
        require_once $vendorPath;

        $mysqli = new \mysqli("db", "appuser", "apppass", "appdb");
        $mysqli->set_charset("utf8mb4");

        $res = $mysqli->query("SELECT month, AVG(sales) AS avg_sales FROM stats_data GROUP BY month");

        $monthOrder = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        $data = [];
        $values = [];
        $labels = [];

        while($row = $res->fetch_assoc()){
            $data[$row['month']] = round($row['avg_sales']);
        }

        foreach($monthOrder as $m){
            $values[] = $data[$m] ?? 0;
            $labels[] = $m;
        }

        $graph = new \Amenadiel\JpGraph\Graph\Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->img->SetMargin(60, 20, 40, 60);
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->title->Set("Month");
        $graph->yaxis->title->Set("Average Sales");

        $barplot = new \Amenadiel\JpGraph\Plot\BarPlot($values);
        $barplot->SetFillColor("blue");
        $barplot->value->Show();
        $barplot->value->SetFormat('%d');
        $graph->Add($barplot);

        $gdImg = $graph->Stroke(_IMG_HANDLER);
        $this->addWatermark($gdImg, "DashaK");

        ob_start();
        imagepng($gdImg);
        $imageData = ob_get_clean();
        imagedestroy($gdImg);
        
        return new Response($imageData, 200, [
            'Content-Type' => 'image/png'
        ]);
    }
    
    public function chart2(): Response 
    {
        $isDocker = file_exists('/var/www/www/vendor/autoload.php');
        $vendorPath = $isDocker ? '/var/www/www/vendor/autoload.php' : __DIR__ . '/../../www/vendor/autoload.php';
        
        require_once $vendorPath;

        $mysqli = new \mysqli("db", "appuser", "apppass", "appdb");
        $mysqli->set_charset("utf8mb4");

        $res = $mysqli->query("SELECT month, AVG(new_clients) AS avg_clients FROM stats_data GROUP BY month");

        $monthOrder = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        $data = [];
        $values = [];
        $labels = [];

        while($row = $res->fetch_assoc()){
            $data[$row['month']] = round($row['avg_clients']);
        }

        foreach($monthOrder as $m){
            $values[] = $data[$m] ?? 0;
            $labels[] = $m;
        }

        $graph = new \Amenadiel\JpGraph\Graph\Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->img->SetMargin(60, 20, 40, 60);
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->title->Set("Month");
        $graph->yaxis->title->Set("Average New Clients");

        $lineplot = new \Amenadiel\JpGraph\Plot\LinePlot($values);
        $lineplot->SetColor("green");
        $lineplot->SetWeight(3);
        $lineplot->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot->mark->SetFillColor("green");
        $lineplot->mark->SetWidth(4);
        $lineplot->value->Show();
        $lineplot->value->SetFormat('%d');
        $graph->Add($lineplot);

        $gdImg = $graph->Stroke(_IMG_HANDLER);
        $this->addWatermark($gdImg, "DashaK");

        ob_start();
        imagepng($gdImg);
        $imageData = ob_get_clean();
        imagedestroy($gdImg);
        
        return new Response($imageData, 200, [
            'Content-Type' => 'image/png'
        ]);
    }
    
    public function chart3(): Response 
    {
        $isDocker = file_exists('/var/www/www/vendor/autoload.php');
        $vendorPath = $isDocker ? '/var/www/www/vendor/autoload.php' : __DIR__ . '/../../www/vendor/autoload.php';
        
        require_once $vendorPath;

        $mysqli = new \mysqli("db", "appuser", "apppass", "appdb");
        $mysqli->set_charset("utf8mb4");

        $res = $mysqli->query("SELECT category, AVG(rating) AS avg_rating FROM stats_data GROUP BY category");

        $data = [];
        $values = [];
        $labels = [];

        while($row = $res->fetch_assoc()){
            $labels[] = $row['category'];
            $values[] = round($row['avg_rating'], 1);
        }

        // Создаем круговую диаграмму
        $graph = new \Amenadiel\JpGraph\Graph\PieGraph(800, 400);
        $graph->img->SetMargin(40, 40, 40, 40);
        $graph->title->Set("Average Rating by Category");

        $pieplot = new \Amenadiel\JpGraph\Plot\PiePlot($values);
        $pieplot->SetLegends($labels);
        $pieplot->SetSliceColors(array('#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'));
        $pieplot->value->Show();
        $pieplot->value->SetFormat('%.1f');
        $graph->Add($pieplot);

        $gdImg = $graph->Stroke(_IMG_HANDLER);
        $this->addWatermark($gdImg, "DashaK");

        ob_start();
        imagepng($gdImg);
        $imageData = ob_get_clean();
        imagedestroy($gdImg);
        
        return new Response($imageData, 200, [
            'Content-Type' => 'image/png'
        ]);
    }
    
    private function addWatermark($gdImg, string $text): void 
    {
        $textColor = imagecolorallocate($gdImg, 128, 128, 128);
        $font = 3;
        $x = imagesx($gdImg) - strlen($text) * 10 - 10;
        $y = imagesy($gdImg) - 20;
        imagestring($gdImg, $font, $x, $y, $text, $textColor);
    }
}