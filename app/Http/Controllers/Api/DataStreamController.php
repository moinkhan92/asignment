<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DataStreamAnalyzer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class DataStreamController extends Controller
{
    public function analyze(Request $request, DataStreamAnalyzer $analyzer)
    {        
        // Validate input
        $validator = Validator::make($request->all(), [
            'stream' => 'required|string',
            'k' => 'required|integer|min:1',
            'top' => 'required|integer|min:1',
            'exclude' => 'sometimes|array'
        ]);

        if ($validator->fails()) {            
            return response()->json(['error' => $validator->errors()], 422);
        }

        $stream = $request->input('stream');
        $k = $request->input('k');
        $top = $request->input('top');
        $exclude = $request->input('exclude', []);

        // Cache key based on request inputs
        $cacheKey = md5($stream . $k . $top . json_encode($exclude));

        // Check cache
        $result = Cache::remember($cacheKey, 600, function () use ($analyzer, $stream, $k, $top, $exclude) {
            return $analyzer->analyze($stream, $k, $top, $exclude);
        });

        return response()->json(['data' => $result]);
    }

}