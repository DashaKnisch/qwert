<?php
function render_svg_from_code(int $num): string {
    $shape = $num % 3;                     
    $color_idx = ($num >> 2) & 0x1F;        
    $w = (($num >> 7) & 0xFF);               
    $h = (($num >> 15) & 0xFF);              

    if ($w < 150) $w += 150;
    if ($h < 150) $h += 150;

    $colors = [
        '#e63946','#f1faee','#a8dadc','#457b9d','#1d3557',
        '#2a9d8f','#e9c46a','#f4a261','#e76f51','#6a994e',
        '#ffbe0b','#ffd166','#06d6a0','#118ab2','#073b4c'
    ];
    $color = $colors[($color_idx + $num) % count($colors)];
    $margin = 20;

    $svg  = "<svg xmlns='http://www.w3.org/2000/svg' width='{$w}' height='{$h}' viewBox='0 0 {$w} {$h}'>";
    $svg .= "<rect width='100%' height='100%' fill='#ffffff' />";

    switch ($shape) {
        case 0: // прямоугольник
            $svg .= "<rect x='{$margin}' y='{$margin}' width='" . ($w - 2*$margin) . "' height='" . ($h - 2*$margin) . "' fill='{$color}' stroke='#000' />";
            break;
        case 1: // круг
            $cx = intval($w/2); $cy = intval($h/2);
            $r = intval(min($w, $h)/2) - $margin;
            if ($r < 20) $r = 20;
            $svg .= "<circle cx='{$cx}' cy='{$cy}' r='{$r}' fill='{$color}' stroke='#000' />";
            break;
        case 2: // треугольник
            $x1 = $w/2; $y1 = $margin;
            $x2 = $margin; $y2 = $h - $margin;
            $x3 = $w - $margin; $y3 = $h - $margin;
            $svg .= "<polygon points='{$x1},{$y1} {$x2},{$y2} {$x3},{$y3}' fill='{$color}' stroke='#000' />";
            break;
    }

    $svg .= "</svg>";
    return $svg;
}
