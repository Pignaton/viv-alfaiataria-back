<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CodigoRastreio implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $codigo = strtoupper(str_replace(' ', '', $value));

        if (strlen($codigo) !== 13) {
            $fail('O código de rastreio deve ter exatamente 13 caracteres (ex: PL123456789BR)');
            return;
        }

        if (!preg_match('/^[A-Z]{2}\d{9}[A-Z]{2}$/', $codigo)) {
            $fail('O código de rastreio deve estar no formato dos Correios (ex: PL123456789BR)');
            return;
        }

        if (!$this->validarPrefixo($codigo)) {
            $fail('O prefixo do código de rastreio não é válido.');
            return;
        }

        if (!$this->validarDigitoVerificador($codigo)) {
            $fail('O código de rastreio parece ser inválido. Verifique os dígitos.');
        }
    }

    protected function validarPrefixo(string $codigo): bool
    {
        $prefixosValidos = [
            'PL', 'PM', 'PQ', 'PR', 'PV', 'PP', 'RM', 'RD', 'RA', 'RC',
            'RJ', 'RK', 'RL', 'RX', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF',
            'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP',
            'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL'
        ];

        $prefixo = substr($codigo, 0, 2);
        return in_array($prefixo, $prefixosValidos);
    }

    protected function validarDigitoVerificador(string $codigo): bool
    {
        try {
            $numeros = substr($codigo, 2, 9);


            $baseCalculo = substr($numeros, 0, 8);
            $dvReal = $numeros[8];

            $soma = 0;
            $pesos = [8, 6, 4, 2, 3, 5, 9, 7];

            for ($i = 0; $i < 8; $i++) {
                $soma += $baseCalculo[$i] * $pesos[$i];
            }

            $resto = $soma % 11;
            $dvCalculado = ($resto == 0) ? 5 : (($resto == 1) ? 0 : (11 - $resto));

            return $dvReal == $dvCalculado;
        } catch (\Exception $e) {
            return false;
        }
    }
}
