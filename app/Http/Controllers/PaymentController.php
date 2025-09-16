<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentMethod;
use Stripe\Customer;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function showCheckout(Request $request)
    {
        $company = auth()->user()->company;
        $plan = Plan::findOrFail($request->plan_id);
        
        return view('payment.checkout', compact('company', 'plan'));
    }

    public function createStripeCheckout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);

        try {
            $company = auth()->user()->company;
            $plan = Plan::findOrFail($request->plan_id);
            $billingCycle = $request->billing_cycle;
            
            // Calcular preço baseado no ciclo
            $price = $billingCycle === 'yearly' ? 
                $plan->price * 12 * 0.8 : // 20% desconto anual
                $plan->price;

            // Criar ou buscar customer no Stripe
            $stripeCustomer = $this->getOrCreateStripeCustomer($company);

            // Criar sessão de checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'customer' => $stripeCustomer->id,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => "Plano {$plan->name} - {$company->name}",
                            'description' => "Assinatura {$billingCycle} do plano {$plan->name}",
                        ],
                        'unit_amount' => intval($price * 100), // Stripe usa centavos
                        'recurring' => [
                            'interval' => $billingCycle === 'yearly' ? 'year' : 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
                'metadata' => [
                    'company_id' => $company->id,
                    'plan_id' => $plan->id,
                    'billing_cycle' => $billingCycle,
                ],
                'subscription_data' => [
                    'trial_period_days' => $company->isTrialing() ? 0 : 14,
                ],
            ]);

            return response()->json([
                'id' => $session->id,
                'url' => $session->url
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createPagSeguroPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|in:credit_card,pix,boleto'
        ]);

        try {
            $company = auth()->user()->company;
            $plan = Plan::findOrFail($request->plan_id);
            $billingCycle = $request->billing_cycle;
            $paymentMethod = $request->payment_method;
            
            // Calcular preço
            $price = $billingCycle === 'yearly' ? 
                $plan->price * 12 * 0.8 : 
                $plan->price;

            // Criar referência única
            $reference = 'SUB-' . $company->id . '-' . $plan->id . '-' . time();

            // Configurar dados do PagSeguro
            $paymentData = [
                'reference' => $reference,
                'amount' => number_format($price, 2, '.', ''),
                'currency' => 'BRL',
                'description' => "Assinatura {$plan->name} - {$company->name}",
                'payment_method' => $paymentMethod,
                'customer' => [
                    'name' => $company->name,
                    'email' => $company->email,
                    'phone' => $company->phone,
                    'document' => $company->document,
                ],
                'metadata' => [
                    'company_id' => $company->id,
                    'plan_id' => $plan->id,
                    'billing_cycle' => $billingCycle,
                ],
                'notification_url' => route('payment.pagseguro.webhook'),
                'redirect_url' => route('payment.success'),
            ];

            // Simular resposta do PagSeguro (implementar integração real)
            $response = $this->simulatePagSeguroPayment($paymentData);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PagSeguro payment error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if ($sessionId) {
            try {
                $session = Session::retrieve($sessionId);
                $company = Company::find($session->metadata->company_id);
                $plan = Plan::find($session->metadata->plan_id);
                
                // Atualizar assinatura
                $this->updateSubscription($company, $plan, $session->metadata->billing_cycle, 'active');
                
                return view('payment.success', compact('company', 'plan'));
                
            } catch (\Exception $e) {
                Log::error('Payment success error: ' . $e->getMessage());
            }
        }
        
        return view('payment.success');
    }

    public function cancel()
    {
        return view('payment.cancel');
    }

    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response('Invalid payload', 400);
        }

        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event['data']['object']);
                break;
            
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event['data']['object']);
                break;
            
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event['data']['object']);
                break;
            
            case 'customer.subscription.deleted':
                $this->handleSubscriptionCanceled($event['data']['object']);
                break;
        }

        return response('Success', 200);
    }

    public function pagSeguroWebhook(Request $request)
    {
        // Implementar webhook do PagSeguro
        Log::info('PagSeguro webhook received', $request->all());
        
        return response('OK', 200);
    }

    private function getOrCreateStripeCustomer(Company $company)
    {
        if ($company->stripe_customer_id) {
            try {
                return Customer::retrieve($company->stripe_customer_id);
            } catch (\Exception $e) {
                Log::warning('Failed to retrieve Stripe customer, creating new one');
            }
        }

        $customer = Customer::create([
            'email' => $company->email,
            'name' => $company->name,
            'phone' => $company->phone,
            'metadata' => [
                'company_id' => $company->id,
            ],
        ]);

        $company->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    private function updateSubscription(Company $company, Plan $plan, string $billingCycle, string $status)
    {
        $price = $billingCycle === 'yearly' ? $plan->price * 12 * 0.8 : $plan->price;
        $endsAt = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

        $company->subscriptions()->where('status', '!=', 'canceled')->update(['status' => 'canceled']);

        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => $status,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'amount' => $price,
            'billing_cycle' => $billingCycle,
        ]);

        $company->update([
            'plan_id' => $plan->id,
            'status' => 'active',
            'trial_ends_at' => null,
        ]);
    }

    private function simulatePagSeguroPayment(array $data)
    {
        // Simulação para desenvolvimento - implementar integração real
        return [
            'payment_id' => 'PAG-' . uniqid(),
            'status' => 'pending',
            'payment_url' => route('payment.success'),
            'qr_code' => $data['payment_method'] === 'pix' ? 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==' : null,
            'boleto_url' => $data['payment_method'] === 'boleto' ? 'https://example.com/boleto.pdf' : null,
        ];
    }

    private function handleCheckoutCompleted($session)
    {
        // Processar checkout completado
        Log::info('Checkout completed', ['session' => $session->id]);
    }

    private function handlePaymentSucceeded($invoice)
    {
        // Processar pagamento bem-sucedido
        Log::info('Payment succeeded', ['invoice' => $invoice->id]);
    }

    private function handlePaymentFailed($invoice)
    {
        // Processar falha no pagamento
        Log::error('Payment failed', ['invoice' => $invoice->id]);
    }

    private function handleSubscriptionCanceled($subscription)
    {
        // Processar cancelamento de assinatura
        Log::info('Subscription canceled', ['subscription' => $subscription->id]);
    }
}