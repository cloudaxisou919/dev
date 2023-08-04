<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Currency;
use App\Models\CustomSettings;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\OldGatewayProducts;
use App\Models\PaymentPlans;
use App\Models\Setting;
// use App\Models\Subscriptions as SubscriptionsModel;
use App\Models\YokassaSubscriptions as SubscriptionsModel;

use App\Models\HowitWorks;
use App\Models\User;
use App\Models\UserAffiliate;
use App\Models\UserOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use YooKassa\Client;
use App\Events\YokassaWebhookEvent;


/**
 * Controls ALL Payment actions of Yokassa
 */
class YokassaController extends Controller
{
    /**
     * Displays Payment Page of Yokassa gateway.
     */
    public static function subscribe($planId, $plan)
    {
        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;

        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->live_client_secret;
        }

        $client = new Client();
        $client->setAuth($shop_id, $key);
        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $plan->price,
                    'currency' => $plan->currency,
                ),
                'confirmation' => array(
                    'type' => 'embedded'
                ),
                'capture' => true,
                'description' => 'Order No. 1',
                'save_payment_method' => true
            ),
            uniqid('', true)
        );

        $confirmation_token = $payment->confirmation->confirmation_token;
        $payment_id = $payment->id;

        return view('panel.user.payment.subscription.payWithYokassa', compact('plan', 'gateway', 'payment_id', 'confirmation_token'));
    }


    /**
     * Handles payment action of Yokassa.
     * 
     * Subscribe payment page posts here.
     */
    public function subscribePay(Request $request)
    {

        $plan = PaymentPlans::find($request->plan);
        $user = Auth::user();
        $settings = Setting::first();

        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;
        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->live_client_secret;
        }

		$client = new Client();
	    $client->setAuth($shop_id, $key);

		$paymentId = $request->payment_id;
  		$payment = $client->getPaymentInfo($paymentId);
			if($payment->paid == true){
			$paymentMethod = $payment->payment_method->type;
            $payment_method_id = $payment->payment_method->id;
            
            $subscription = new SubscriptionsModel();
            $subscription->user_id = $user->id;
            $subscription->name = $plan->id;
            $subscription->payment_method_id = $payment_method_id;
            $subscription->subscription_status = 'active';
            $subscription->plan_id = $plan->id;
            $frq = $plan->frequency;
            if($frq == 'monthly') $subscription->next_pay_at = Carbon::now()->addMonth();
            if($frq == 'yearly') $subscription->next_pay_at = Carbon::now()->addyear();
            $subscription->save();

			$payment = new UserOrder();
			$payment->order_id = Str::random(12);
			$payment->plan_id = $plan->id;
			$payment->user_id = $user->id;
			$payment->payment_type = 'Credit, Debit Card';
			$payment->price = $plan->price;
			$payment->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
			$payment->status = 'Success';
			$payment->country = $user->country ?? 'Unknown';
			$payment->save();

			$user->remaining_words += $plan->total_words;
			$user->remaining_images += $plan->total_images;
			$user->save();
            createActivity($user->id, 'Subscribed', $plan->name . ' Plan', null);

			return redirect()->route('dashboard.index')->with(['message' => 'Thank you for your purchase. Enjoy your remaining words and images.', 'type' => 'success']);
		} else {
			return redirect()->route('dashboard.index')->with(['message' => 'You are failed your purchase. If you paid for this, please cantact us', 'type' => 'failed']);
		}
    }

    /**
     * handle payment with saved payment
     * 
     */
    
    public function handleSubscribePay(Request $request)
    {
        $user = Auth::user();
        
        
        $activeSub = SubscriptionsModel::where([['stripe_status', '=', 'active'], ['user_id', '=', $userId]])->first();
        $plan_id = $activeSub->plan_id;
        $plan = PaymentPlans::where('id', $plan_id)->first();

        $payment_method_id = $activeSub->$payment_method_id;
        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->live_client_secret;
        }

        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $plan->price,
                    'currency' => $plan->currency,
                ),
                'capture' => true,
                'payment_method_id' => $payment_method_id,
                'description' => 'Auto payment',
            ),
            uniqid('', true)
        );


        if($payment->paid == true) return 'success';
        else return 'false';
    }

    /**
     * Cancels current subscription plan
     */
    public static function subscribeCancel()
    {

        $user = Auth::user();
        $settings = Setting::first();

        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;

        $activeSub = SubscriptionsModel::where([['subscription_status', '=', 'active'],['user_id','=', $user->id]])->first();
        // $activeSub = $user->subscriptions()->where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->first();

        if ($activeSub != null) {
            $plan = PaymentPlans::where('id', $activeSub->plan_id)->first();

            $recent_words = $user->remaining_words - $plan->total_words;
            $recent_images = $user->remaining_images - $plan->total_images;

            // $user->subscription($activeSub->name)->cancelNow();

            // $user->remaining_words = $recent_words < 0 ? 0 : $recent_words;
            // $user->remaining_images = $recent_images < 0 ? 0 : $recent_images;
            $user->save();
            $activeSub->payment_method_id = '';
            $activeSub->subscription_status = '';
            $activeSub->save();
            createActivity($user->id, 'Cancelled', 'Subscription plan', null);

            return back()->with(['message' => 'Your subscription is cancelled succesfully on the server. Please check your wallet and stop auto payment', 'type' => 'success']);
        }
        return back()->with(['message' => 'Could not find active subscription. Nothing changed!', 'type' => 'error']);
    }


    /**
     * Displays Payment Page of Yokassa gateway for prepaid plans.
     */
    public static function prepaid($planId, $plan, $incomingException = null)
    {

        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;

        if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->live_client_secret;
        }
        
        $client = new Client();
        $client->setAuth($shop_id, $key);
        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $plan->price,
                    'currency' => $plan->currency,
                ),
                'confirmation' => array(
                    'type' => 'embedded'
                ),
                'capture' => true,
                'description' => 'Order No. 1'
            ),
            uniqid('', true)
        );
        $confirmation_token = $payment->confirmation->confirmation_token;
		$payment_id = $payment->id;
        return view('panel.user.payment.prepaid.payWithYokassa', compact('plan', 'gateway', 'confirmation_token', 'payment_id'));
    }


    /**
     * Handles payment action of Yokassa.
     * 
     * Prepaid payment page posts here.
     */
    public function prepaidPay(Request $request)
    {
        $plan = PaymentPlans::find($request->plan);
        $user = Auth::user();
        $settings = Setting::first();
        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;
		
		if ($gateway->mode == 'sandbox') {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->sandbox_client_secret;
        } else {
            $shop_id = $gateway->sandbox_client_id;
            $key = $gateway->live_client_secret;
        }
		
		$client = new Client();
	    $client->setAuth($shop_id, $key);

		$paymentId = $request->payment_id;
  		$payment = $client->getPaymentInfo($paymentId);
			if($payment->paid == true){
			$paymentMethod = $payment->payment_method->type;

			$payment = new UserOrder();
			$payment->order_id = Str::random(12);
			$payment->plan_id = $plan->id;
			$payment->type = 'prepaid';
			$payment->user_id = $user->id;
			$payment->payment_type = 'Credit, Debit Card';
			$payment->price = $plan->price;
			$payment->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
			$payment->status = 'Success';
			$payment->country = $user->country ?? 'Unknown';
			$payment->save();

			$user->remaining_words += $plan->total_words;
			$user->remaining_images += $plan->total_images;
			$user->save();
            createActivity($user->id, 'Purchased', $plan->name . ' Token Pack', null);

			return redirect()->route('dashboard.index')->with(['message' => 'Thank you for your purchase. Enjoy your remaining words and images.', 'type' => 'success']);
		} else {
			return redirect()->route('dashboard.index')->with(['message' => 'You are failed your purchase. If you paid for this, please cantact us', 'type' => 'failed']);
		}        
    }


    /**
     * Saves Membership plan product in Yokassa gateway.
     * @param planId ID of plan in PaymentPlans model.
     * @param productName Name of the product, plain text
     * @param price Price of product
     * @param frequency Time interval of subscription, month / annual
     * @param type Type of product subscription/one-time
     */

    public static function getSubscriptionDaysLeft()
    {

        // $plan = PaymentPlans::find($request->plan);
        $user = Auth::user();
        $settings = Setting::first();

        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;

        $activeSub = SubscriptionsModel::where([['subscription_status', '=', 'active'], ['user_id', '=', $user->id]])->first();

        if ($activeSub->subscription_status == 'active') {
            // return Carbon\Carbon::createFromTimeStamp($activeSub->next_pay_at);
            return \Carbon\Carbon::now()->diffInDays($activeSub->next_pay_at);
        } else {
            return;
        }

        // return $activeSub->current_period_end;

    }


    public static function getSubscriptionRenewDate()
    {

        // $plan = PaymentPlans::find($request->plan);
        $user = Auth::user();
        $settings = Setting::first();

        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;

        $activeSub = SubscriptionsModel::where([['subscription_status', '=', 'active'], ['user_id', '=', $userId]])->first();

        return \Carbon\Carbon::createFromTimeStamp($activeSub->next_pay_at)->format('F jS, Y');
    }

    /**
     * Checks status directly from gateway and updates database if cancelled or suspended.
     */
    public static function getSubscriptionStatus($incomingUserId = null)
    {

        // $plan = PaymentPlans::find($request->plan);
        if($incomingUserId != null){
            $user = User::where('id', $incomingUserId)->first();
        }else{
            $user = Auth::user();
        }
        $settings = Setting::first();

        $gateway = Gateways::where("code", "yokassa")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $activeSub = SubscriptionsModel::where([['subscription_status', '=', 'active'], ['user_id', '=', $userId]])->first();

        if($activeSub != null){
            if ($subscription['status'] == 'ACTIVE'){
                return true;
            }else{
                $activeSub->stripe_status = 'cancelled';
                $activeSub->ends_at = \Carbon\Carbon::now();
                $activeSub->save();
                return false;
            }
        }

        return false;
    }


    public static function checkIfTrial()
    {

        // $plan = PaymentPlans::find($request->plan);
        $user = Auth::user();
        $settings = Setting::first();

        $gateway = Gateways::where("code", "stripe")->first();
        if ($gateway == null) {
            abort(404);
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;

        $sub = $user->subscriptions()->where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->first();
        if ($sub != null) {
            if ($sub->paid_with == 'stripe') {
                // $activeSub = $sub->asStripeSubscription();
                // return $activeSub->onTrial();
                return $user->subscription($sub->name)->onTrial();
            }
        }

        return false;
    }

    function verifyIncomingJson(Request $request){
        try{
            $gateway = Gateways::where("code", "yokassa")->first();
            $webhook_event = $request->getContent();
            if($webhook_event == null){return false;}
            if(isJson($webhook_event)==false){return false;}
            Log::info("+++++++++++++");
            Log::info($request);
            $payload = $request->getContent();
            $yokassa_ip_array = [
                '185.71.76.0/27',
                '185.71.77.0/27',
                '77.75.153.0/25',
                '77.75.156.11',
                '77.75.156.35',
                '77.75.154.128/25',
                '2a02:5180::/32'
            ];
            $ip_address = $request->ip();
            if(in_array($ip_address, $yokassa_ip_array)) return $payload;
            else return false;
        } catch (\Exception $th) {
            error_log("(Webhooks) Yokassa::verifyIncomingJson(): ".$th->getMessage());
        }

        return false;
    }

    public function handleWebhook(Request $request){

        // Log::info($request->getContent());
        // $verified = $request->getContent();

        $verified = self::verifyIncomingJson($request);

        if($verified != null){

            // Retrieve the JSON payload
            $payload = $verified;

            // Fire the event with the payload
            event(new YokassaWebhookEvent($payload));
        
            return response()->json(['success' => true]);
        
        }else{
            // Incoming json is NOT verified
            abort(404);
        }
    }
}
