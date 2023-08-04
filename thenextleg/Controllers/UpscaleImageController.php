<?php

namespace TheNextLeg\Controllers;

use Illuminate\Support\Facades\Auth;
use TheNextLeg\TheNextLegService;
use TheNextLeg\Models\TheNextLeg;
use TheNextLeg\Models\TheNextLegImages;
use TheNextLeg\Models\TheNextLegProgressLog;
use Illuminate\Support\Facades\Storage;

class UpscaleImageController extends Controller
{
    public function checkAIProgress(TheNextLegService $theNextLegService, $messageId)
    {
        $user = Auth::user();
        $data = $theNextLegService->message($messageId);
        $content = $data->getContent();
        $responseData = json_decode($content, true);
        if (isset($responseData['progress']) && $responseData['progress'] <= 100) {
            $thenextlegs = Thenextleg::orderBy('created_at', 'desc')->take(1)->first();
            $entry = new TheNextLegProgressLog();
            $entry->nextleg_id = $thenextlegs->id;
            $entry->progress_response = json_encode($responseData);
            $entry->save();

            if (isset($responseData['progress']) && $responseData['progress'] == 100 && isset($responseData['response']['buttonMessageId'])) {
                $thenextlegs->button_id = $responseData['response']['buttonMessageId'];
                $thenextlegs->save();
            }
            
            if (isset($responseData['response']['buttonMessageId']) && !empty($responseData['response']['buttonMessageId'])) {
                if ($user->remaining_images > 1) {
                    $user->remaining_images = $user->remaining_images - 1;
                    $user->save();
                } else {
                    $user->remaining_images = 0;
                    $data = array(
                        'errors' => ['You have no credits left. Please consider upgrading your plan.'],
                    );
                }
            }
            if (isset($responseData['response']['imageUrl']) && !empty($responseData['response']['imageUrl'])) {
                $count = 1;
                $thenextlegs = Thenextleg::orderBy('created_at', 'desc')->take(1)->first();
                    $thenextlegImages = new TheNextLegImages();
                    $thenextlegImages->thenextleg_id = $thenextlegs->id;
                    $thenextlegImages->image_index = $count++;
                    $thenextlegImages->upscale_image = $responseData['response']['imageUrl'];
                    $thenextlegImages->upscaling_at = date('Y-m-d H:i:s');
                    $thenextlegImages->save();

                   // $imageContents = base64_decode($responseData['response']['imageUrl']);
                   // $filename = uniqid('image_') . '.png';
                   // Storage::disk('public')->put($filename, $imageContents);
                   // $thenextlegImages->upscale_local_path = 'uploads/' . $filename;
                   // $thenextlegImages->save();

                $data = view('append_image',  compact('thenextlegImages'))->render();
            }

        } else {
            $data = array(
                'errors' => ['Your Progress is Incomplete.'],
            );
        }
       
        return response()->json(['data' => $data, 'progress' => $responseData['progress']]);
    }
}
