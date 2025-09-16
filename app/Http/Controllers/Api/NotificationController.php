<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $query = DB::table('notifications')->where('company_id', $companyId);
        
        // Filter by read status
        if ($request->has('unread_only')) {
            $query->where('is_read', false);
        }
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }
        
        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->input('priority'));
        }
        
        // Sort by creation date (newest first)
        $query->orderBy('created_at', 'desc');
        
        // Pagination
        $perPage = $request->input('per_page', 20);
        $offset = ($request->input('page', 1) - 1) * $perPage;
        
        $total = $query->count();
        $notifications = $query->offset($offset)->limit($perPage)->get();
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $request->input('page', 1),
                'last_page' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $updated = DB::table('notifications')
                    ->where('id', $id)
                    ->where('company_id', $companyId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now()
                    ]);
        
        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Notificação marcada como lida'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notificação não encontrada'
        ], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $updated = DB::table('notifications')
                    ->where('company_id', $companyId)
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now()
                    ]);
        
        return response()->json([
            'success' => true,
            'message' => "Todas as {$updated} notificações foram marcadas como lidas"
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(string $id): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $deleted = DB::table('notifications')
                    ->where('id', $id)
                    ->where('company_id', $companyId)
                    ->delete();
        
        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Notificação excluída'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notificação não encontrada'
        ], 404);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $count = DB::table('notifications')
                   ->where('company_id', $companyId)
                   ->where('is_read', false)
                   ->count();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Create a new notification
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'priority' => 'required|in:low,medium,high,critical',
            'user_id' => 'nullable|exists:users,id'
        ]);
        
        $validated['company_id'] = auth()->user()->company_id;
        $validated['data'] = json_encode($validated['data'] ?? []);
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        
        $id = DB::table('notifications')->insertGetId($validated);
        
        $notification = DB::table('notifications')->where('id', $id)->first();
        
        return response()->json([
            'success' => true,
            'message' => 'Notificação criada com sucesso',
            'data' => $notification
        ], 201);
    }

    /**
     * Get notification statistics
     */
    public function stats(): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        $stats = [
            'total' => DB::table('notifications')->where('company_id', $companyId)->count(),
            'unread' => DB::table('notifications')->where('company_id', $companyId)->where('is_read', false)->count(),
            'by_priority' => DB::table('notifications')
                              ->where('company_id', $companyId)
                              ->select('priority', DB::raw('COUNT(*) as count'))
                              ->groupBy('priority')
                              ->get(),
            'by_type' => DB::table('notifications')
                          ->where('company_id', $companyId)
                          ->select('type', DB::raw('COUNT(*) as count'))
                          ->groupBy('type')
                          ->get(),
            'recent' => DB::table('notifications')
                         ->where('company_id', $companyId)
                         ->whereDate('created_at', '>=', now()->subDays(7))
                         ->count()
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
