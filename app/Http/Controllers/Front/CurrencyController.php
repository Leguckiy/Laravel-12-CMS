<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    /**
     * Set currency in session and return success for AJAX.
     */
    public function set(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => [
                'required',
                'integer',
                Rule::exists('currencies', 'id')->where('status', true),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $request->session()->put('currency_id', (int) $request->input('currency_id'));

        return response()->json(['success' => true]);
    }
}
