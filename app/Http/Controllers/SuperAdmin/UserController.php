<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Http\Requests\UserRequest;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HandlesFileUploads;
    public function index(Request $request)
    {
        $query = User::query()->with(['company', 'roles']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20);

        // Estatísticas
        $stats = [
            'total' => User::count(),
            'active' => User::where('active', true)->count(),
            'inactive' => User::where('active', false)->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count()
        ];

        $companies = Company::orderBy('name')->get();
        $roles = Role::where('name', '!=', 'super-admin')->get();

        return view('super-admin.users.index', compact('users', 'stats', 'companies', 'roles'));
    }

    public function show(User $user)
    {
        $user->load(['company', 'roles', 'permissions']);
        
        return view('super-admin.users.show', compact('user'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $roles = Role::where('name', '!=', 'super-admin')->get();
        
        return view('super-admin.users.create', compact('companies', 'roles'));
    }

    public function edit(User $user)
    {
        $companies = Company::orderBy('name')->get();
        $roles = Role::where('name', '!=', 'super-admin')->get();
        
        return view('super-admin.users.create', compact('user', 'companies', 'roles'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
            'role' => 'nullable|exists:roles,name',
            'active' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'company_id' => $validated['company_id'],
                'active' => $validated['active'] ?? true,
                'phone' => $validated['phone'] ?? null
            ];

            // Upload de avatar se fornecido
            if ($request->hasFile('avatar')) {
                try {
                    $uploadResult = $this->optimizeImageForContext($request->file('avatar'), 'user_avatar');
                    $userData['avatar'] = $uploadResult['path'];
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro no upload do avatar: ' . $e->getMessage()
                    ], 422);
                }
            }

            $user = User::create($userData);

            // Atribuir role se especificada
            if (!empty($validated['role'])) {
                $user->assignRole($validated['role']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuário criado com sucesso!',
                'user' => $user->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
            'role' => 'nullable|exists:roles,name',
            'active' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'company_id' => $validated['company_id'],
                'active' => $validated['active'] ?? true,
                'phone' => $validated['phone'] ?? null
            ];

            // Atualizar senha se fornecida
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            // Upload de novo avatar se fornecido
            if ($request->hasFile('avatar')) {
                try {
                    // Deletar avatar antigo se existir
                    if ($user->avatar) {
                        $this->deleteFile($user->avatar);
                    }

                    $uploadResult = $this->optimizeImageForContext($request->file('avatar'), 'user_avatar');
                    $updateData['avatar'] = $uploadResult['path'];
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro no upload do avatar: ' . $e->getMessage()
                    ], 422);
                }
            }

            $user->update($updateData);

            // Atualizar role
            if (!empty($validated['role'])) {
                $user->syncRoles([$validated['role']]);
            } else {
                $user->syncRoles([]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'user' => $user->fresh()->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            // Verificar se não é super admin
            if ($user->hasRole('super-admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir um super administrador.'
                ], 422);
            }

            // Deletar avatar se existir
            if ($user->avatar) {
                $this->deleteFile($user->avatar);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(User $user)
    {
        try {
            // Verificar se não é super admin
            if ($user->hasRole('super-admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível alterar o status de um super administrador.'
                ], 422);
            }

            $user->update(['active' => !$user->active]);

            return response()->json([
                'success' => true,
                'message' => $user->active ? 'Usuário ativado com sucesso!' : 'Usuário desativado com sucesso!',
                'active' => $user->active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status do usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = User::query()->with(['company', 'roles']);

        // Aplicar mesmos filtros da listagem
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        $users = $query->get();

        $filename = 'usuarios_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalhos
            fputcsv($file, [
                'ID',
                'Nome',
                'Email',
                'Empresa',
                'Funções',
                'Status',
                'Último Login',
                'Criado em'
            ]);

            // Dados
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->company ? $user->company->name : 'Sem empresa',
                    $user->roles->pluck('name')->implode(', ') ?: 'Usuário',
                    $user->active ? 'Ativo' : 'Inativo',
                    $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca',
                    $user->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete,change_role',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'nullable|exists:roles,name'
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];

        // Verificar se não há super admins na seleção
        $superAdmins = User::whereIn('id', $userIds)
                          ->whereHas('roles', function($q) {
                              $q->where('name', 'super-admin');
                          })
                          ->count();

        if ($superAdmins > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível executar ações em lote em super administradores.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            switch ($action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['active' => true]);
                    $message = 'Usuários ativados com sucesso!';
                    break;

                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['active' => false]);
                    $message = 'Usuários desativados com sucesso!';
                    break;

                case 'delete':
                    User::whereIn('id', $userIds)->delete();
                    $message = 'Usuários excluídos com sucesso!';
                    break;

                case 'change_role':
                    if (empty($validated['role'])) {
                        throw new \Exception('Role não especificada para mudança em lote.');
                    }
                    
                    $users = User::whereIn('id', $userIds)->get();
                    foreach ($users as $user) {
                        $user->syncRoles([$validated['role']]);
                    }
                    $message = 'Funções alteradas com sucesso!';
                    break;

                default:
                    throw new \Exception('Ação não reconhecida.');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar ação em lote: ' . $e->getMessage()
            ], 500);
        }
    }
}