<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Storage};
use App\Models\{Plot, Document, Share};

class PlotManagementController extends Controller
{
    public function store(Request $request)
    {
        // Validating request data
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'total_share' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // returning error message on validation fail
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        //get user
        $user = auth()->user();

        //store the plot
        $plot = Plot::create([
            'address' => $request->address,
            'size' => $request->size,
            'price' => $request->price,
            'status' => $request->status,
            'total_share' => $request->total_share,
            'available_share' => $request->total_share,
            'sold_share' => 0,
            'description' => $request->description,
        ]);

        Share::create([
            'user_id' => $user->id,
            'plot_id' => $plot->id,
            'share' => $request->total_share,
            'share_status' => '1st_owner',
            'share_price' => $request->price,
            'share_number' => $request->total_share    
        ]);

        //if request has file
        if ($request->hasFile('file')) {
            //get file
            $file = $request->file('file');

            //get file name
            $filename = $file->getClientOriginalName();
            
            $path = $file->store('public/plots/'.$plot->id.'/');

            //get img src url
            $url = asset('storage/plots/' . $plot->id . '/' . basename($path));

            //store the document
            Document::create([
                'plot_id' => $plot->id,
                'name' => $filename,
                'path' => $url,
            ]);
        }

        //get plot documents
        $documents = $plot->documents;

        $plot->documents = $documents;

        // returning response
        return response()->json([
            'success' => true,
            'message' => 'Plot created successfully',
            'plot' => $plot,
        ]);
    }

    public function list()
    {
        //get plots
        $plots = Plot::all();

        //get plot documents
        foreach ($plots as $plot) {
            $documents = $plot->documents;

            $plot->documents = $documents;
        }

        // returning response
        return response()->json([
            'success' => true,
            'plots' => $plots,
        ]);
    }

    public function show($id)
    {
        //get plot
        $plot = Plot::find($id);

        //get plot documents
        $documents = $plot->documents;

        // adding documents to plot
        $plot->documents = $documents;
        
        // get plot shares
        $shares = $plot->shares;

        // adding shares to plot
        $plot->shares = $shares;

        // returning response
        return response()->json([
            'success' => true,
            'plot' => $plot,
        ]);
    }

    public function update(Request $request, $id){

         // Validating request data
         $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'total_share' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // returning error message on validation fail
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        //get plot
        $plot = Plot::find($id);

        //if plot not found
        if (!$plot) {
            return response()->json([
                'success' => false,
                'message' => 'Plot not found',
            ]);
        }

        //update plot
        $plot->update([
            'address' => $request->address,
            'size' => $request->size,
            'price' => $request->price,
            'status' => $request->status,
            'total_share' => $request->total_share,
            'description' => $request->description,
        ]);

        //if request has file
        if ($request->hasFile('file')) {

            //delete old file
            Storage::deleteDirectory('public/plots/'.$plot->id);

            //get file
            $file = $request->file('file');

            //get file name
            $filename = $file->getClientOriginalName();
            
            $path = $file->store('public/plots/'.$plot->id.'/');

            //get img src url
            $url = asset('storage/plots/' . $plot->id . '/' . basename($path));

            //store the document
            Document::create([
                'plot_id' => $plot->id,
                'name' => $filename,
                'path' => $url,
            ]);
        }
        
        //get plot documents
        $documents = $plot->documents;

        $plot->documents = $documents;

        // returning response
        return response()->json([
            'success' => true,
            'message' => 'Plot updated successfully',
            'plot' => $plot,
        ]);

    }

    public function delete($id)
    {
        //get plot
        $plot = Plot::find($id);

        //if plot not found
        if (!$plot) {
            return response()->json([
                'success' => false,
                'message' => 'Plot not found',
            ]);
        }

        //delete plot documents directory
        Storage::deleteDirectory('public/plots/'.$plot->id);

        //delete plot
        $plot->delete();

        // returning response
        return response()->json([
            'success' => true,
            'message' => 'Plot deleted successfully',
        ]);
    }
}
