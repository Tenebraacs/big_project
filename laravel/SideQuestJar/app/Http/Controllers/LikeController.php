<?php

namespace App\Http\Controllers;

use App\Models\Jar;
use App\Models\JarQuest;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    
    public function toggleJar(Jar $jar): JsonResponse
    {
        $userId = Auth::id();

        $existingLike = $jar->likes()->where('user_id', $userId)->first();

        if ($existingLike){
            $existingLike->delete();

            return  response()->json([
                'message' => 'Jar unliked',
                'liked' => false,
                'likes_count' => $jar->likes()->count,
            ]);
        }

        $jar->likes()->create([
            'user_id' => $userId,
        ]);

        return  response()->json([
                'message' => 'Jar liked',
                'liked' => true,
                'likes_count' => $jar->likes()->count,
        ]);
    }

    public function toggleQuest(JarQuest $quest): JsonResponse
    {
        $userId = Auth::id();

        $existingLike = $quest->likes()->where('user_id', $userId)->first();

        if ($existingLike){
            $existingLike->delete();

            return  response()->json([
                'message' => 'Quest unliked',
                'liked' => false,
                'likes_count' => $quest->likes()->count,
            ]);
        }

        $quest->likes()->create([
            'user_id' => $userId,
        ]);

        return  response()->json([
                'message' => 'Quest liked',
                'liked' => true,
                'likes_count' => $quest->likes()->count,
        ]);
    }



}
