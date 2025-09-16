<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    /**
     * Get all settings for the company
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $query = DB::table('company_settings')->where('company_id', $companyId);
        
        // Filter public settings only if requested
        if ($request->has('public_only')) {
            $query->where('is_public', true);
        }
        
        $settings = $query->get();
        
        // Format settings for response
        $formattedSettings = [];
        foreach ($settings as $setting) {
            $value = $setting->value;
            
            // Parse value based on type
            switch ($setting->type) {
                case 'json':
                    $value = json_decode($value, true);
                    break;
                case 'boolean':
                    $value = (bool) $value;
                    break;
                case 'number':
                    $value = is_numeric($value) ? (float) $value : $value;
                    break;
            }
            
            $formattedSettings[$setting->key] = [
                'value' => $value,
                'type' => $setting->type,
                'description' => $setting->description,
                'is_public' => (bool) $setting->is_public,
                'updated_at' => $setting->updated_at
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $formattedSettings
        ]);
    }

    /**
     * Get a specific setting
     */
    public function show(string $key): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $setting = DB::table('company_settings')
                    ->where('company_id', $companyId)
                    ->where('key', $key)
                    ->first();
        
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Configuração não encontrada'
            ], 404);
        }
        
        // Parse value based on type
        $value = $setting->value;
        switch ($setting->type) {
            case 'json':
                $value = json_decode($value, true);
                break;
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'number':
                $value = is_numeric($value) ? (float) $value : $value;
                break;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'key' => $setting->key,
                'value' => $value,
                'type' => $setting->type,
                'description' => $setting->description,
                'is_public' => (bool) $setting->is_public,
                'updated_at' => $setting->updated_at
            ]
        ]);
    }

    /**
     * Update or create a setting
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string|max:255',
                'value' => 'required',
                'type' => 'required|in:string,json,boolean,number',
                'description' => 'nullable|string|max:500',
                'is_public' => 'boolean'
            ]);
            
            $companyId = auth()->user()->company_id;
            
            // Process value based on type
            $value = $validated['value'];
            switch ($validated['type']) {
                case 'json':
                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value);
                    } else {
                        // Validate JSON string
                        json_decode($value);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new ValidationException('Invalid JSON format');
                        }
                    }
                    break;
                case 'boolean':
                    $value = $value ? '1' : '0';
                    break;
                case 'number':
                    if (!is_numeric($value)) {
                        throw new ValidationException('Value must be a number');
                    }
                    $value = (string) $value;
                    break;
                default:
                    $value = (string) $value;
            }
            
            // Upsert setting
            DB::table('company_settings')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'key' => $validated['key']
                ],
                [
                    'value' => $value,
                    'type' => $validated['type'],
                    'description' => $validated['description'] ?? null,
                    'is_public' => $validated['is_public'] ?? false,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Configuração salva com sucesso'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update multiple settings at once
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*.key' => 'required|string|max:255',
                'settings.*.value' => 'required',
                'settings.*.type' => 'required|in:string,json,boolean,number',
                'settings.*.description' => 'nullable|string|max:500',
                'settings.*.is_public' => 'boolean'
            ]);
            
            $companyId = auth()->user()->company_id;
            
            DB::beginTransaction();
            
            try {
                foreach ($validated['settings'] as $settingData) {
                    // Process value based on type
                    $value = $settingData['value'];
                    switch ($settingData['type']) {
                        case 'json':
                            if (is_array($value) || is_object($value)) {
                                $value = json_encode($value);
                            } else {
                                json_decode($value);
                                if (json_last_error() !== JSON_ERROR_NONE) {
                                    throw new \Exception("Invalid JSON format for key: {$settingData['key']}");
                                }
                            }
                            break;
                        case 'boolean':
                            $value = $value ? '1' : '0';
                            break;
                        case 'number':
                            if (!is_numeric($value)) {
                                throw new \Exception("Value must be a number for key: {$settingData['key']}");
                            }
                            $value = (string) $value;
                            break;
                        default:
                            $value = (string) $value;
                    }
                    
                    // Upsert setting
                    DB::table('company_settings')->updateOrInsert(
                        [
                            'company_id' => $companyId,
                            'key' => $settingData['key']
                        ],
                        [
                            'value' => $value,
                            'type' => $settingData['type'],
                            'description' => $settingData['description'] ?? null,
                            'is_public' => $settingData['is_public'] ?? false,
                            'updated_at' => now(),
                            'created_at' => now()
                        ]
                    );
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Configurações salvas com sucesso'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete a setting
     */
    public function destroy(string $key): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $deleted = DB::table('company_settings')
                    ->where('company_id', $companyId)
                    ->where('key', $key)
                    ->delete();
        
        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Configuração excluída com sucesso'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Configuração não encontrada'
        ], 404);
    }

    /**
     * Reset settings to default values
     */
    public function reset(): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        // Delete all settings for the company
        DB::table('company_settings')->where('company_id', $companyId)->delete();
        
        // Create default settings
        $defaultSettings = $this->getDefaultSettings();
        
        foreach ($defaultSettings as $key => $setting) {
            DB::table('company_settings')->insert([
                'company_id' => $companyId,
                'key' => $key,
                'value' => $setting['value'],
                'type' => $setting['type'],
                'description' => $setting['description'],
                'is_public' => $setting['is_public'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Configurações redefinidas para os valores padrão'
        ]);
    }

    /**
     * Get default settings
     */
    private function getDefaultSettings(): array
    {
        return [
            'low_stock_alert' => [
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Ativar alertas de estoque baixo',
                'is_public' => true
            ],
            'auto_backup' => [
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Ativar backup automático',
                'is_public' => false
            ],
            'backup_frequency' => [
                'value' => 'daily',
                'type' => 'string',
                'description' => 'Frequência do backup automático (daily, weekly, monthly)',
                'is_public' => false
            ],
            'dashboard_layout' => [
                'value' => json_encode([
                    'widgets' => ['stats', 'recent_movements', 'low_stock', 'charts'],
                    'theme' => 'light'
                ]),
                'type' => 'json',
                'description' => 'Layout personalizado do dashboard',
                'is_public' => true
            ],
            'currency' => [
                'value' => 'BRL',
                'type' => 'string',
                'description' => 'Moeda padrão do sistema',
                'is_public' => true
            ],
            'timezone' => [
                'value' => 'America/Sao_Paulo',
                'type' => 'string',
                'description' => 'Fuso horário da empresa',
                'is_public' => true
            ],
            'max_file_size' => [
                'value' => '10',
                'type' => 'number',
                'description' => 'Tamanho máximo de upload em MB',
                'is_public' => false
            ]
        ];
    }
}
