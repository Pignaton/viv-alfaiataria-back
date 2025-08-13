<?php

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;

if (!function_exists('uploadToR2')) {
    function uploadToR2($file, $directory = 'tecidos')
    {
       // try {
            if (!$file->isValid()) {
                throw new Exception('Arquivo inválido.');
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = trim($directory, '/') . '/' . uniqid() . '.' . $extension;

            $fileContents = file_get_contents($file->getRealPath());
            if ($fileContents === false) {
                throw new Exception('Não foi possível ler o conteúdo do arquivo.');
            }

            $uploaded = Storage::disk('cloudflare_r2')->put($fileName, $fileContents, [
                'visibility' => 'public',
            ]);

            if (!$uploaded) {
                throw new Exception('Falha no upload do arquivo para R2.');
            }

            if (!Storage::disk('cloudflare_r2')->exists($fileName)) {
                throw new Exception('Upload aparentemente bem-sucedido, mas arquivo não encontrado no R2.');
            }

            return Storage::disk('cloudflare_r2')->url($fileName);

      /*  } catch (\Exception $e) {

            \Log::error('Erro no upload para R2: ' . $e->getMessage());
            throw new Exception('Erro no upload para R2: ' . $e->getMessage());
        }*/
    }
}

if (!function_exists('deleteFromR2')) {
    function deleteFromR2($url)
    {
        try {
            $path = ltrim(parse_url($url, PHP_URL_PATH), '/');

            if (empty($path)) {
                throw new Exception('Caminho inválido para deletar.');
            }

            return Storage::disk('cloudflare_r2')->delete($path);
        } catch (\Exception $e) {
            throw new Exception('Erro ao deletar do R2: ' . $e->getMessage());
        }
    }
}
