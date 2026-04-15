<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Initiate PayFast Payment
     */
    public function index(Request $request, $amount)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $amount = (float) $amount;
        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Invalid amount.');
        }

        // Generate Basket ID
        $basket_id = rand(11111111, 99999999);

        // Create initial pending deposit record
        $this->createPayment($user->id, $basket_id, $amount);

        // Get credentials from settings
        $merchant_id = Setting::where('key', 'payfast_merchant_id')->value('value');
        $secured_key = Setting::where('key', 'payfast_secured_key')->value('value');

        if (!$merchant_id || !$secured_key) {
             Log::error('PayFast credentials missing in settings.');
             return redirect()->back()->with('error', 'Payment gateway configuration error.');
        }

        // Generate Token
        $tokenData = $this->createAccessToken($amount, $basket_id, $merchant_id, $secured_key);

        if (isset($tokenData['ACCESS_TOKEN'])) {
            $accessToken = $tokenData['ACCESS_TOKEN'];
            $merchantName = $tokenData['NAME'];

            $data = [
                'CURRENCY_CODE' => 'PKR',
                'MERCHANT_ID' => $merchant_id,
                'MERCHANT_NAME' => $merchantName,
                'TOKEN' => $accessToken,
                'PROCCODE' => '00',
                'TXNAMT' => $amount,
                'CUSTOMER_MOBILE_NO' => $user->phone ?? '03000000000', // Fallback if phone missing
                'CUSTOMER_EMAIL_ADDRESS' => $user->email,
                'SIGNATURE' => Str::random(20),
                'VERSION' => Str::random(20),
                'TXNDESC' => 'Add Funds via PayFast',
                'SUCCESS_URL' => route('payment.success'),
                'FAILURE_URL' => route('payment.fail'),
                'BASKET_ID' => $basket_id,
                'ORDER_DATE' => Carbon::now()->format('Y-m-d H:i:s'),
                'CHECKOUT_URL' => route('payment.notify'), // Or separate notification URL
            ];

            return view('payment.payfast_form', compact('data'));

        } else {
            Log::error('PayFast Token Error: ' . json_encode($tokenData));
            return redirect()->back()->with('error', 'Failed to initiate payment gateway.');
        }
    }

    /**
     * Generate Access Token from PayFast API
     */
    private function createAccessToken($amount, $basket_id, $merchant_id, $secured_key)
    {
        try {
            $response = Http::withOptions(['verify' => false])->asForm()->post('https://ipg1.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken', [
                'MERCHANT_ID' => $merchant_id,
                'SECURED_KEY' => $secured_key,
                'BASKET_ID' => $basket_id,
                'TXNAMT' => $amount,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PayFast Token Exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create Pending Deposit Record
     * Using existing Deposit model, storing basket_id in admin_note for tracking
     */
    private function createPayment($user_id, $basket_id, $amount)
    {
        Deposit::create([
            'user_id' => $user_id,
            'payment_method' => 'payfast', // We might need to add this to enum/logic later
            'phone_number' => Auth::user()->phone ?? 'N/A',
            'amount' => $amount,
            'status' => 'pending',
            'admin_note' => "PayFast Basket ID: $basket_id", // Storing basket_id here
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Payment Success Callback
     */
    public function success(Request $request)
    {
        $basket_id = $request->input('basket_id') ?? $request->input('BASKET_ID');
        $err_msg = $request->input('err_msg') ?? 'Success';

        $deposit = Deposit::where('admin_note', 'LIKE', "%$basket_id%")->first();

        if ($deposit && $deposit->status == 'pending') {
            $deposit->status = 'approved';
            $deposit->admin_note .= " | Success: " . $err_msg;
            $deposit->approved_at = now();
            $deposit->save();

            // Credit User Balance
            $user = User::find($deposit->user_id);
            if ($user) {
                $user->incrementBalance($deposit->amount);
            }
        }

        return view('payment.success', compact('request'));
    }

    /**
     * Payment Failure Callback
     */
    public function fail(Request $request)
    {
        $basket_id = $request->input('basket_id') ?? $request->input('BASKET_ID');
        $err_msg = $request->input('err_msg') ?? 'Failed';

        $deposit = Deposit::where('admin_note', 'LIKE', "%$basket_id%")->first();

        if ($deposit && $deposit->status == 'pending') {
            $deposit->status = 'declined';
            $deposit->admin_note .= " | Failed: " . $err_msg;
            $deposit->save();
        }

        return view('payment.fail', compact('request'));
    }

    /**
     * Payment Notification (Optional Webhook)
     */
    public function notify(Request $request)
    {
        // Handle server-to-server notification if needed
        return response()->json(['status' => 'received']);
    }
}
