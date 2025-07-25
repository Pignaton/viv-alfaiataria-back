<?php
if (!function_exists('formatarMedidas')) {
    function formatarMedidas($medidas)
    {
        return $medidas->map(function ($medida) {
            return ucfirst($medida->nome) . ': ' . $medida->valor . ' ' . $medida->unidade;
        })->implode('<br>');
    }
}


if (!function_exists('calculateMovingAverage')) {
    function calculateMovingAverage($data, $windowSize)
    {
        $movingAverages = [];
        for ($i = 0; $i < count($data); $i++) {
            $start = max(0, $i - $windowSize + 1);
            $slice = array_slice($data, $start, $windowSize);
            $movingAverages[] = array_sum($slice) / count($slice);
        }
        return $movingAverages;
    }

}

if (!function_exists('badgeStatus')) {
    function badgeStatus($data)
    {
        switch ($data) {
            case 'entregue':
            case 'enviado':
                return 'primary';
                break;
            case 'Pago':
                return 'success';
                break;
            case 'pendente':
            case 'aguardando_pagamento':
                return 'warning';
                break;
            case 'processando':
                return 'info';
                break;
            default:
                return 'secondary';
                break;
        }
    }

}
