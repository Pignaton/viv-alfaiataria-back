<?php
if (!function_exists('formatarMedidas')) {
    function formatarMedidas($medidas)
    {
        return $medidas->map(function ($medida) {
            return ucfirst($medida->nome) . ': ' . $medida->valor . ' ' . $medida->unidade;
        })->implode('<br>');
    }
}
