<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HandlesFileUploads;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view users')->only(['index', 'show']);
        $this->middleware('permission:create users')->only(['create', 'store']);
        $this->middleware('permission:edit users')->only(['edit', 'update']);
        $this->middleware('permission:delete users')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = User::with('roles')->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
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

        $users = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total' => User::where('company_id', $companyId)->count(),
            'active' => User::where('company_id', $companyId)->where('active', true)->count(),
            'inactive' => User::where('company_id', $companyId)->where('active', false)->count(),
            'new_this_month' => User::where('company_id', $companyId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Roles da empresa (excluir super-admin)
        $roles = Role::whereNotIn('name', ['super-admin'])->get();

        return view('users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'active' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ]);

        DB::beginTransaction();

        try {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'company_id' => auth()->user()->company_id,
                'active' => $validated['active'] ?? true,
                'phone' => $validated['phone'] ?? null
            ];

            // Upload de avatar se fornecido
            if ($request->hasFile('avatar')) {
                try {
                    $uploadResult = $this->optimizeImageForContext($request->file('avatar'), 'user_avatar');
                    $userData['avatar'] = $uploadResult['path'];
                } catch (\Exception $e) {
                    DB::rollback();
                    return back()->withErrors(['avatar' => 'Erro no upload do avatar: ' . $e->getMessage()]);
                }
            }

            $user = User::create($userData);

            // Atribuir role se especificada e não for super-admin
            if (!empty($validated['role']) && $validated['role'] !== 'super-admin') {
                $user->assignRole($validated['role']);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário criado com sucesso!',
                    'user' => $user->load('roles')
                ]);
            }

            return redirect()->route('users.index')
                ->with('success', 'Usuário criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar usuário: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao criar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Verificar se usuário pertence à mesma empresa
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'active' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ]);

        DB::beginTransaction();

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'active' => $validated['active'] ?? true,
                'phone' => $validated['phone'] ?? null
            ];

            // Atualizar senha apenas se fornecida
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
                    DB::rollback();
                    return back()->withErrors(['avatar' => 'Erro no upload do avatar: ' . $e->getMessage()]);
                }
            }

            $user->update($updateData);

            // Atualizar role se não for super-admin
            if (!empty($validated['role']) && $validated['role'] !== 'super-admin') {
                $user->syncRoles([$validated['role']]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário atualizado com sucesso!',
                    'user' => $user->fresh()->load('roles')
                ]);
            }

            return redirect()->route('users.index')
                ->with('success', 'Usuário atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Verificar se usuário pertence à mesma empresa
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        // Não permitir exclusão do próprio usuário
        if ($user->id === auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode excluir seu próprio usuário.'
                ], 422);
            }
            
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        // Não permitir exclusão do último admin da empresa
        if ($user->hasRole('admin')) {
            $adminCount = User::role('admin')
                ->where('active', true)
                ->where('company_id', $user->company_id)
                ->count();
            if ($adminCount <= 1) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível excluir o último administrador ativo da empresa.'
                    ], 422);
                }
                
                return redirect()->route('users.index')
                    ->with('error', 'Não é possível excluir o último administrador ativo da empresa.');
            }
        }

        try {
            // Deletar avatar se existir
            if ($user->avatar) {
                $this->deleteFile($user->avatar);
            }

            $user->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário excluído com sucesso!'
                ]);
            }

            return redirect()->route('users.index')
                ->with('success', 'Usuário excluído com sucesso!');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('users.index')
                ->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        // Não permitir desativar o próprio usuário
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode desativar seu próprio usuário.');
        }

        // Não permitir desativar o último admin
        if ($user->hasRole('admin') && $user->active) {
            $activeAdminCount = User::role('admin')->where('active', true)->where('id', '!=', $user->id)->count();
            if ($activeAdminCount < 1) {
                return redirect()->route('users.index')
                    ->with('error', 'Não é possível desativar o último administrador ativo.');
            }
        }

        $user->update(['active' => !$user->active]);

        $status = $user->active ? 'ativado' : 'desativado';
        return redirect()->route('users.index')
            ->with('success', "Usuário {$status} com sucesso!");
    }
}
