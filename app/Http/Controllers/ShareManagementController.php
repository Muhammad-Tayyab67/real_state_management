<?php

namespace App\Http\Controllers;
use App\Models\{Plot, Share};
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class ShareManagementController extends Controller
{
    public function BuyShare(Request $request)
    {
        // Validating request data
        $validator = Validator::make($request->all(), [
            'plot_id' => 'required|string',
            'share_number' => 'required|numeric|min:1',
        ]);

        // returning error message on validation fail
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        //Get Plot
        $plot = Plot::find($request->plot_id);
        
        if(!$plot){
            return response()->json([
                'success' => false,
                'message' => 'Plot not found',
            ]);
        }

        //Get User
        $user = auth()->user();

        $share_number = $request->share_number;

        //Check if share is available
        if($plot->available_share < $share_number){
            return response()->json([
                'success' => false,
                'message' => 'You can not buy more than '.$plot->available_share.'% shares',
            ]);
        }

        //Total share price = total share % of total price
        $total_share_price = $plot->total_share / $plot->price * 100;

        //Share price = share number % of total share price
        $share_price = $total_share_price * $share_number / 100;
        
        //Create share
        $share = Share::create([
            'user_id' => $user->id,
            'plot_id' => $plot->id,
            'share_number' => $share_number,
            'share_price' => $share_price,
            'share_status' => 'share_holder',
        ]);

        //Update plot
        $plot->update([
            'available_share' => $plot->available_share - $share_number,
            'sold_share' => $plot->sold_share + $share_number,
        ]);

        //all shares
        $shares = $plot->shares;

        $plot->shares = $shares;

        $plot->documents = $plot->documents;

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Share bought successfully',
            'plot' => $plot,
        ]);
    }
}
