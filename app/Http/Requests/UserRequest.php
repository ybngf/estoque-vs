<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin') && $this->user()->can('manage-users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/' // Apenas letras e espaços
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id'
            ],
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
            ],
            'is_active' => [
                'required',
                'boolean'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[(]?[0-9\s\-\(\)]{10,20}$/' // Formato de telefone flexível
            ]
        ];

        // Validação de senha
        if (!$isUpdate) {
            // Criação: senha obrigatória
            $rules['password'] = [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ];
        } else {
            // Edição: senha opcional
            $rules['password'] = [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ];
        }

        // Validações específicas por role
        if ($this->role) {
            switch ($this->role) {
                case 'super-admin':
                    // Super admin não pode estar vinculado a uma empresa específica
                    $rules['company_id'] = [
                        'nullable',
                        'integer',
                        'exists:companies,id'
                    ];
                    break;
                    
                case 'admin':
                case 'manager':
                case 'employee':
                    // Outros roles devem ter empresa obrigatória
                    $rules['company_id'][0] = 'required';
                    break;
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.regex' => 'O nome deve conter apenas letras e espaços.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ter um formato válido.',
            'email.unique' => 'Este e-mail já está sendo usado por outro usuário.',
            'company_id.required' => 'A empresa é obrigatória.',
            'company_id.exists' => 'A empresa selecionada não existe.',
            'role.required' => 'O cargo/função é obrigatório.',
            'role.exists' => 'O cargo/função selecionado não existe.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'phone.regex' => 'O telefone deve ter um formato válido.',
            'is_active.required' => 'O status do usuário é obrigatório.',
            'is_active.boolean' => 'O status deve ser ativo ou inativo.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'company_id' => 'empresa',
            'role' => 'cargo/função',
            'password' => 'senha',
            'password_confirmation' => 'confirmação da senha',
            'phone' => 'telefone',
            'is_active' => 'status'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpar telefone removendo caracteres especiais para validação
        if ($this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^\d\+]/', '', $this->phone)
            ]);
        }

        // Converter is_active para boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        // Normalizar email
        if ($this->email) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }

        // Normalizar nome
        if ($this->name) {
            $this->merge([
                'name' => trim($this->name)
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'message' => 'Os dados fornecidos são inválidos.',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validação adicional: não permitir criar super-admin se não for super-admin
            if ($this->role === 'super-admin' && !$this->user()->hasRole('super-admin')) {
                $validator->errors()->add('role', 'Apenas super administradores podem criar outros super administradores.');
            }

            // Validação adicional: não permitir desativar próprio usuário
            if ($this->route('user') && $this->route('user')->id === $this->user()->id && !$this->is_active) {
                $validator->errors()->add('is_active', 'Você não pode desativar sua própria conta.');
            }

            // Validação adicional: verificar se a empresa está ativa
            if ($this->company_id) {
                $company = \App\Models\Company::find($this->company_id);
                if ($company && !$company->is_active) {
                    $validator->errors()->add('company_id', 'A empresa selecionada não está ativa.');
                }
            }
        });
    }
}