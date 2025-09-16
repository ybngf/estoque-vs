<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    /**
     * List all backups for the company
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $query = DB::table('backups')->where('company_id', $companyId);
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Sort by creation date (newest first)
        $query->orderBy('created_at', 'desc');
        
        // Pagination
        $perPage = $request->input('per_page', 20);
        $offset = ($request->input('page', 1) - 1) * $perPage;
        
        $total = $query->count();
        $backups = $query->offset($offset)->limit($perPage)->get();
        
        // Format file sizes
        foreach ($backups as $backup) {
            $backup->size_formatted = $this->formatBytes($backup->size_bytes);
            $backup->metadata = json_decode($backup->metadata, true);
        }
        
        return response()->json([
            'success' => true,
            'data' => $backups,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $request->input('page', 1),
                'last_page' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Create a new backup
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:database,files,full',
            'description' => 'nullable|string|max:255'
        ]);
        
        $companyId = auth()->user()->company_id;
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$companyId}_{$validated['type']}_{$timestamp}";
        
        // Create backup record
        $backupId = DB::table('backups')->insertGetId([
            'company_id' => $companyId,
            'filename' => $filename,
            'type' => $validated['type'],
            'status' => 'pending',
            'is_automatic' => false,
            'started_at' => now(),
            'metadata' => json_encode([
                'description' => $validated['description'] ?? null,
                'created_by' => auth()->user()->name,
                'created_by_id' => auth()->id()
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Process backup in background (simulate)
        $this->processBackup($backupId, $validated['type'], $companyId);
        
        return response()->json([
            'success' => true,
            'message' => 'Backup iniciado com sucesso',
            'backup_id' => $backupId
        ], 201);
    }

    /**
     * Get backup details
     */
    public function show(string $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $backup = DB::table('backups')
                   ->where('id', $id)
                   ->where('company_id', $companyId)
                   ->first();
        
        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup não encontrado'
            ], 404);
        }
        
        $backup->size_formatted = $this->formatBytes($backup->size_bytes);
        $backup->metadata = json_decode($backup->metadata, true);
        
        return response()->json([
            'success' => true,
            'data' => $backup
        ]);
    }

    /**
     * Delete a backup
     */
    public function destroy(string $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $backup = DB::table('backups')
                   ->where('id', $id)
                   ->where('company_id', $companyId)
                   ->first();
        
        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup não encontrado'
            ], 404);
        }
        
        // Delete file if exists
        if ($backup->storage_path && Storage::exists($backup->storage_path)) {
            Storage::delete($backup->storage_path);
        }
        
        // Delete record
        DB::table('backups')->where('id', $id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Backup excluído com sucesso'
        ]);
    }

    /**
     * Download a backup file
     */
    public function download(string $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $backup = DB::table('backups')
                   ->where('id', $id)
                   ->where('company_id', $companyId)
                   ->where('status', 'completed')
                   ->first();
        
        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup não encontrado ou não está completo'
            ], 404);
        }
        
        if (!$backup->storage_path || !Storage::exists($backup->storage_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Arquivo de backup não encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'download_url' => route('api.backup.file', ['id' => $id, 'token' => encrypt($id)])
        ]);
    }

    /**
     * Get backup statistics
     */
    public function stats(): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $stats = [
            'total_backups' => DB::table('backups')->where('company_id', $companyId)->count(),
            'completed_backups' => DB::table('backups')->where('company_id', $companyId)->where('status', 'completed')->count(),
            'failed_backups' => DB::table('backups')->where('company_id', $companyId)->where('status', 'failed')->count(),
            'total_size' => DB::table('backups')->where('company_id', $companyId)->where('status', 'completed')->sum('size_bytes'),
            'by_type' => DB::table('backups')
                          ->where('company_id', $companyId)
                          ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(size_bytes) as total_size'))
                          ->groupBy('type')
                          ->get(),
            'recent_backups' => DB::table('backups')
                              ->where('company_id', $companyId)
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get()
        ];
        
        $stats['total_size_formatted'] = $this->formatBytes($stats['total_size']);
        
        foreach ($stats['by_type'] as $type) {
            $type->total_size_formatted = $this->formatBytes($type->total_size);
        }
        
        foreach ($stats['recent_backups'] as $backup) {
            $backup->size_formatted = $this->formatBytes($backup->size_bytes);
        }
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Process backup (simplified simulation)
     */
    private function processBackup($backupId, $type, $companyId)
    {
        try {
            // Update status to processing
            DB::table('backups')->where('id', $backupId)->update([
                'status' => 'processing',
                'updated_at' => now()
            ]);
            
            // Simulate backup process
            $size = 0;
            $storagePath = null;
            
            switch ($type) {
                case 'database':
                    // Simulate database export
                    $size = rand(1024*1024, 10*1024*1024); // 1MB to 10MB
                    $storagePath = "backups/company_{$companyId}/database_" . now()->format('Y-m-d_H-i-s') . ".sql";
                    break;
                    
                case 'files':
                    // Simulate file backup
                    $size = rand(5*1024*1024, 50*1024*1024); // 5MB to 50MB
                    $storagePath = "backups/company_{$companyId}/files_" . now()->format('Y-m-d_H-i-s') . ".tar.gz";
                    break;
                    
                case 'full':
                    // Simulate full backup
                    $size = rand(10*1024*1024, 100*1024*1024); // 10MB to 100MB
                    $storagePath = "backups/company_{$companyId}/full_" . now()->format('Y-m-d_H-i-s') . ".tar.gz";
                    break;
            }
            
            // Update backup as completed
            DB::table('backups')->where('id', $backupId)->update([
                'status' => 'completed',
                'size_bytes' => $size,
                'storage_path' => $storagePath,
                'completed_at' => now(),
                'updated_at' => now()
            ]);
            
        } catch (\Exception $e) {
            // Update backup as failed
            DB::table('backups')->where('id', $backupId)->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
