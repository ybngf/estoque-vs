<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Subscription;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin') && $this->user()->can('manage-subscriptions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $subscriptionId = $this->route('subscription') ? $this->route('subscription')->id : null;
        
        return [
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
                // Verificar se a empresa não tem assinatura ativa (exceto na edição)
                function ($attribute, $value, $fail) use ($subscriptionId) {
                    $activeSubscription = Subscription::where('company_id', $value)
                        ->where('status', 'active')
                        ->when($subscriptionId, function ($query) use ($subscriptionId) {
                            return $query->where('id', '!=', $subscriptionId);
                        })
                        ->first();
                    
                    if ($activeSubscription) {
                        $fail('Esta empresa já possui uma assinatura ativa.');
                    }
                }
            ],
            'plan_id' => [
                'required',
                'integer',
                'exists:plans,id',
                // Verificar se o plano está ativo
                function ($attribute, $value, $fail) {
                    $plan = \App\Models\Plan::find($value);
                    if ($plan && !$plan->is_active) {
                        $fail('O plano selecionado não está ativo.');
                    }
                }
            ],
            'status' => [
                'required',
                Rule::in([
                    Subscription::STATUS_ACTIVE,
                    Subscription::STATUS_INACTIVE,
                    Subscription::STATUS_TRIALING,
                    Subscription::STATUS_CANCELED,
                    Subscription::STATUS_PAST_DUE
                ])
            ],
            'billing_cycle' => [
                'required',
                Rule::in(['monthly', 'yearly'])
            ],
            'starts_at' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'ends_at' => [
                'nullable',
                'date',
                'after:starts_at',
                // Validar se a data de término é compatível com o ciclo
                function ($attribute, $value, $fail) {
                    if ($value && $this->starts_at) {
                        $startDate = new \DateTime($this->starts_at);
                        $endDate = new \DateTime($value);
                        $diffMonths = $startDate->diff($endDate)->m + ($startDate->diff($endDate)->y * 12);
                        
                        $expectedMonths = $this->billing_cycle === 'yearly' ? 12 : 1;
                        
                        if ($diffMonths < $expectedMonths) {
                            $fail('A data de término deve ser pelo menos ' . $expectedMonths . ' mês(es) após a data de início.');
                        }
                    }
                }
            ],
            'payment_method' => [
                'nullable',
                'string',
                'max:50',
                Rule::in(['credit_card', 'bank_slip', 'pix', 'bank_transfer'])
            ],
            'external_id' => [
                'nullable',
                'string',
                'max:100',
                // Verificar unicidade do external_id se fornecido
                Rule::unique('subscriptions', 'external_id')->ignore($subscriptionId)
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'A empresa é obrigatória.',
            'company_id.exists' => 'A empresa selecionada não existe.',
            'plan_id.required' => 'O plano é obrigatório.',
            'plan_id.exists' => 'O plano selecionado não existe.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status selecionado é inválido.',
            'billing_cycle.required' => 'O ciclo de cobrança é obrigatório.',
            'billing_cycle.in' => 'O ciclo de cobrança deve ser mensal ou anual.',
            'starts_at.required' => 'A data de início é obrigatória.',
            'starts_at.date' => 'A data de início deve ser uma data válida.',
            'starts_at.after_or_equal' => 'A data de início deve ser hoje ou uma data futura.',
            'ends_at.date' => 'A data de término deve ser uma data válida.',
            'ends_at.after' => 'A data de término deve ser posterior à data de início.',
            'payment_method.in' => 'O método de pagamento selecionado é inválido.',
            'external_id.unique' => 'Este ID externo já está sendo usado.',
            'external_id.max' => 'O ID externo não pode ter mais de 100 caracteres.',
            'notes.max' => 'As observações não podem ter mais de 1000 caracteres.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_id' => 'empresa',
            'plan_id' => 'plano',
            'status' => 'status',
            'billing_cycle' => 'ciclo de cobrança',
            'starts_at' => 'data de início',
            'ends_at' => 'data de término',
            'payment_method' => 'método de pagamento',
            'external_id' => 'ID externo',
            'notes' => 'observações'
        ];
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