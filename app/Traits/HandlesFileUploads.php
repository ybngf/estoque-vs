<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait HandlesFileUploads
{
    /**
     * Upload e processa uma imagem
     */
    protected function uploadImage(UploadedFile $file, string $directory, array $options = []): array
    {
        $options = array_merge([
            'max_width' => 800,
            'max_height' => 600,
            'quality' => 85,
            'format' => 'jpg',
            'generate_thumbnail' => true,
            'thumbnail_width' => 150,
            'thumbnail_height' => 150
        ], $options);

        // Validar o arquivo
        $this->validateImageFile($file, $options);

        // Gerar nome único
        $filename = $this->generateUniqueFilename($file, $options['format']);
        
        // Processar e salvar a imagem principal
        $image = Image::make($file);
        
        // Redimensionar se necessário
        if ($image->width() > $options['max_width'] || $image->height() > $options['max_height']) {
            $image->resize($options['max_width'], $options['max_height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Salvar imagem principal
        $mainPath = "{$directory}/{$filename}";
        Storage::disk('public')->put($mainPath, $image->encode($options['format'], $options['quality']));

        $result = [
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'path' => $mainPath,
            'url' => Storage::disk('public')->url($mainPath),
            'size' => Storage::disk('public')->size($mainPath),
            'mime_type' => $file->getMimeType(),
            'width' => $image->width(),
            'height' => $image->height()
        ];

        // Gerar thumbnail se solicitado
        if ($options['generate_thumbnail']) {
            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailPath = "{$directory}/thumbnails/{$thumbnailFilename}";
            
            $thumbnail = Image::make($file)
                ->fit($options['thumbnail_width'], $options['thumbnail_height'])
                ->encode($options['format'], $options['quality']);
                
            Storage::disk('public')->put($thumbnailPath, $thumbnail);
            
            $result['thumbnail'] = [
                'filename' => $thumbnailFilename,
                'path' => $thumbnailPath,
                'url' => Storage::disk('public')->url($thumbnailPath),
                'size' => Storage::disk('public')->size($thumbnailPath),
                'width' => $options['thumbnail_width'],
                'height' => $options['thumbnail_height']
            ];
        }

        return $result;
    }

    /**
     * Upload de arquivo geral
     */
    protected function uploadFile(UploadedFile $file, string $directory, array $options = []): array
    {
        $options = array_merge([
            'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
            'max_size' => 10240 // KB
        ], $options);

        // Validar arquivo
        $this->validateFile($file, $options);

        // Gerar nome único
        $extension = $file->getClientOriginalExtension();
        $filename = $this->generateUniqueFilename($file, $extension);
        
        // Salvar arquivo
        $path = $file->storeAs($directory, $filename, 'public');

        return [
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension
        ];
    }

    /**
     * Deletar arquivo e sua thumbnail (se existir)
     */
    protected function deleteFile(string $path): bool
    {
        $deleted = false;
        
        // Deletar arquivo principal
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            $deleted = true;
        }

        // Deletar thumbnail se existir
        $directory = dirname($path);
        $filename = basename($path);
        $thumbnailPath = "{$directory}/thumbnails/thumb_{$filename}";
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }

        return $deleted;
    }

    /**
     * Validar arquivo de imagem
     */
    private function validateImageFile(UploadedFile $file, array $options): void
    {
        // Validar tipo MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP.');
        }

        // Validar tamanho
        $maxSize = $options['max_size'] ?? 5120; // 5MB por padrão
        if ($file->getSize() > ($maxSize * 1024)) {
            throw new \InvalidArgumentException("Arquivo muito grande. Tamanho máximo: {$maxSize}KB");
        }

        // Validar dimensões (se especificado)
        if (isset($options['min_width']) || isset($options['min_height'])) {
            $imageSize = getimagesize($file->getPathname());
            if ($imageSize === false) {
                throw new \InvalidArgumentException('Arquivo de imagem inválido.');
            }

            [$width, $height] = $imageSize;
            
            if (isset($options['min_width']) && $width < $options['min_width']) {
                throw new \InvalidArgumentException("Largura mínima: {$options['min_width']}px");
            }
            
            if (isset($options['min_height']) && $height < $options['min_height']) {
                throw new \InvalidArgumentException("Altura mínima: {$options['min_height']}px");
            }
        }
    }

    /**
     * Validar arquivo geral
     */
    private function validateFile(UploadedFile $file, array $options): void
    {
        // Validar extensão
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $options['allowed_extensions'])) {
            $allowed = implode(', ', $options['allowed_extensions']);
            throw new \InvalidArgumentException("Extensão não permitida. Use: {$allowed}");
        }

        // Validar tamanho
        if ($file->getSize() > ($options['max_size'] * 1024)) {
            throw new \InvalidArgumentException("Arquivo muito grande. Tamanho máximo: {$options['max_size']}KB");
        }
    }

    /**
     * Gerar nome único para arquivo
     */
    private function generateUniqueFilename(UploadedFile $file, string $extension = null): string
    {
        $extension = $extension ?: $file->getClientOriginalExtension();
        
        // Remover caracteres especiais do nome original
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($originalName);
        
        // Gerar nome único
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "{$safeName}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Otimizar imagem para diferentes contextos
     */
    protected function optimizeImageForContext(UploadedFile $file, string $context): array
    {
        $contexts = [
            'user_avatar' => [
                'max_width' => 300,
                'max_height' => 300,
                'quality' => 90,
                'format' => 'jpg',
                'generate_thumbnail' => true,
                'thumbnail_width' => 80,
                'thumbnail_height' => 80,
                'min_width' => 100,
                'min_height' => 100
            ],
            'company_logo' => [
                'max_width' => 500,
                'max_height' => 200,
                'quality' => 90,
                'format' => 'png',
                'generate_thumbnail' => true,
                'thumbnail_width' => 150,
                'thumbnail_height' => 60,
                'min_width' => 200,
                'min_height' => 50
            ],
            'product_image' => [
                'max_width' => 800,
                'max_height' => 600,
                'quality' => 85,
                'format' => 'jpg',
                'generate_thumbnail' => true,
                'thumbnail_width' => 200,
                'thumbnail_height' => 150
            ]
        ];

        if (!isset($contexts[$context])) {
            throw new \InvalidArgumentException("Contexto de imagem inválido: {$context}");
        }

        return $this->uploadImage($file, $context, $contexts[$context]);
    }

    /**
     * Gerar URL de placeholder para imagens
     */
    protected function getPlaceholderUrl(string $context, int $width = 300, int $height = 300): string
    {
        $placeholders = [
            'user_avatar' => "https://ui-avatars.com/api/?name=User&size={$width}&background=6c757d&color=ffffff",
            'company_logo' => "https://via.placeholder.com/{$width}x{$height}/6c757d/ffffff?text=Logo",
            'product_image' => "https://via.placeholder.com/{$width}x{$height}/dee2e6/6c757d?text=Produto"
        ];

        return $placeholders[$context] ?? "https://via.placeholder.com/{$width}x{$height}/dee2e6/6c757d?text=Imagem";
    }

    /**
     * Limpar uploads órfãos (arquivos não referenciados)
     */
    protected function cleanupOrphanedFiles(string $directory, array $activeFiles = []): int
    {
        $deletedCount = 0;
        $files = Storage::disk('public')->allFiles($directory);
        
        foreach ($files as $file) {
            $filename = basename($file);
            
            // Pular thumbnails (serão limpas com suas imagens principais)
            if (str_starts_with($filename, 'thumb_')) {
                continue;
            }
            
            // Se o arquivo não está na lista de ativos, deletar
            if (!in_array($filename, $activeFiles)) {
                $this->deleteFile($file);
                $deletedCount++;
            }
        }
        
        return $deletedCount;
    }
}