<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin') && $this->user()->can('manage-plans');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $planId = $this->route('plan') ? $this->route('plan')->id : null;
        
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('plans', 'name')->ignore($planId)
            ],
            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9-]+$/', // Apenas letras minúsculas, números e hífen
                Rule::unique('plans', 'slug')->ignore($planId)
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'monthly_price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999.99',
                'regex:/^\d+(\.\d{1,2})?$/' // Até 2 casas decimais
            ],
            'yearly_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/', // Até 2 casas decimais
                // Validar se o preço anual é menor que 12x o mensal
                function ($attribute, $value, $fail) {
                    if ($this->monthly_price && $value > ($this->monthly_price * 12)) {
                        $fail('O preço anual deve ser menor que 12 vezes o preço mensal para ser vantajoso.');
                    }
                }
            ],
            'trial_days' => [
                'nullable',
                'integer',
                'min:0',
                'max:365'
            ],
            'features' => [
                'nullable',
                'array',
                'max:20' // Máximo 20 recursos
            ],
            'features.*' => [
                'required',
                'string',
                'max:200',
                'distinct' // Não permitir recursos duplicados
            ],
            'limits' => [
                'nullable',
                'array',
                'max:15' // Máximo 15 limites
            ],
            'limits.*.key' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z_]+$/', // Apenas letras minúsculas e underscore
                'distinct'
            ],
            'limits.*.value' => [
                'required',
                'string',
                'max:100'
            ],
            'is_active' => [
                'required',
                'boolean'
            ],
            'is_featured' => [
                'nullable',
                'boolean'
            ],
            'max_users' => [
                'nullable',
                'integer',
                'min:1',
                'max:10000'
            ],
            'max_storage_gb' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:10000'
            ],
            'support_level' => [
                'nullable',
                'string',
                Rule::in(['basic', 'standard', 'premium', 'enterprise'])
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do plano é obrigatório.',
            'name.unique' => 'Já existe um plano com este nome.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.unique' => 'Este slug já está sendo usado.',
            'slug.regex' => 'O slug deve conter apenas letras minúsculas, números e hífen.',
            'monthly_price.required' => 'O preço mensal é obrigatório.',
            'monthly_price.numeric' => 'O preço mensal deve ser um número.',
            'monthly_price.min' => 'O preço mensal deve ser maior ou igual a zero.',
            'monthly_price.regex' => 'O preço mensal deve ter no máximo 2 casas decimais.',
            'yearly_price.required' => 'O preço anual é obrigatório.',
            'yearly_price.numeric' => 'O preço anual deve ser um número.',
            'yearly_price.min' => 'O preço anual deve ser maior ou igual a zero.',
            'yearly_price.regex' => 'O preço anual deve ter no máximo 2 casas decimais.',
            'trial_days.integer' => 'O período de teste deve ser um número inteiro.',
            'trial_days.min' => 'O período de teste deve ser maior ou igual a zero.',
            'trial_days.max' => 'O período de teste não pode ser maior que 365 dias.',
            'features.array' => 'Os recursos devem ser uma lista.',
            'features.max' => 'Você pode adicionar no máximo 20 recursos.',
            'features.*.required' => 'O recurso não pode estar vazio.',
            'features.*.max' => 'Cada recurso deve ter no máximo 200 caracteres.',
            'features.*.distinct' => 'Não é permitido recursos duplicados.',
            'limits.array' => 'Os limites devem ser uma lista.',
            'limits.max' => 'Você pode adicionar no máximo 15 limites.',
            'limits.*.key.required' => 'A chave do limite é obrigatória.',
            'limits.*.key.regex' => 'A chave deve conter apenas letras minúsculas e underscore.',
            'limits.*.key.distinct' => 'Não é permitido chaves duplicadas.',
            'limits.*.value.required' => 'O valor do limite é obrigatório.',
            'is_active.required' => 'O status do plano é obrigatório.',
            'is_active.boolean' => 'O status deve ser ativo ou inativo.',
            'max_users.integer' => 'O máximo de usuários deve ser um número inteiro.',
            'max_users.min' => 'O máximo de usuários deve ser pelo menos 1.',
            'max_storage_gb.numeric' => 'O armazenamento deve ser um número.',
            'max_storage_gb.min' => 'O armazenamento deve ser pelo menos 0.1 GB.',
            'support_level.in' => 'O nível de suporte selecionado é inválido.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'slug' => 'slug',
            'description' => 'descrição',
            'monthly_price' => 'preço mensal',
            'yearly_price' => 'preço anual',
            'trial_days' => 'período de teste',
            'features' => 'recursos',
            'limits' => 'limites',
            'is_active' => 'status',
            'is_featured' => 'plano em destaque',
            'max_users' => 'máximo de usuários',
            'max_storage_gb' => 'armazenamento máximo',
            'support_level' => 'nível de suporte'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Gerar slug automaticamente se não fornecido
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Converter valores boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        if ($this->has('is_featured')) {
            $this->merge([
                'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        // Filtrar features vazias
        if ($this->features) {
            $this->merge([
                'features' => array_values(array_filter($this->features, function($feature) {
                    return !empty(trim($feature));
                }))
            ]);
        }

        // Filtrar limites vazios
        if ($this->limits) {
            $filteredLimits = [];
            foreach ($this->limits as $limit) {
                if (!empty(trim($limit['key'] ?? '')) && !empty(trim($limit['value'] ?? ''))) {
                    $filteredLimits[] = [
                        'key' => trim($limit['key']),
                        'value' => trim($limit['value'])
                    ];
                }
            }
            $this->merge(['limits' => $filteredLimits]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificar se há pelo menos um recurso
            if (empty($this->features)) {
                $validator->errors()->add('features', 'O plano deve ter pelo menos um recurso.');
            }

            // Validar se preços não são zerados em planos pagos
            if ($this->monthly_price == 0 && $this->yearly_price == 0 && !$this->trial_days) {
                $validator->errors()->add('monthly_price', 'Planos gratuitos devem ter período de teste definido.');
            }

            // Validar se trial_days é compatível com preços
            if ($this->trial_days > 0 && $this->monthly_price == 0 && $this->yearly_price == 0) {
                // Ok, plano gratuito com período limitado
            } elseif ($this->trial_days > 30 && ($this->monthly_price > 0 || $this->yearly_price > 0)) {
                $validator->errors()->add('trial_days', 'Planos pagos não devem ter período de teste maior que 30 dias.');
            }

            // Verificar se não está desativando um plano que tem assinaturas ativas
            if ($this->route('plan') && !$this->is_active) {
                $activeSubscriptions = \App\Models\Subscription::where('plan_id', $this->route('plan')->id)
                    ->where('status', 'active')
                    ->count();
                
                if ($activeSubscriptions > 0) {
                    $validator->errors()->add('is_active', 'Não é possível desativar um plano que possui assinaturas ativas.');
                }
            }
        });
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
}