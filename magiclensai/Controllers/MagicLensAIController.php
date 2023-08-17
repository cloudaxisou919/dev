<?php

namespace MagicLensAI\Controllers;

use Illuminate\Support\Facades\Auth;
use MagicLensAI\MagicLensAIService;
use Illuminate\Http\Request;
use MagicLensAI\Models\TheNextLeg;
use MagicLensAI\Models\TheNextLegImages;
use MagicLensAI\Models\TheNextLegProgressLog;
use App\Models\Setting;
use App\Models\SettingTwo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MagicLensAIController extends Controller
{
    protected $settings;
    const STORAGE_S3 = 's3';

    public function __construct()
    {
        //Settings
        $this->settings = Setting::first();
        $this->settings_two = SettingTwo::first();
        // Fetch the Site Settings object with openai_api_secret
        $apiKeys = explode(',', $this->settings->openai_api_secret);
        $apiKey = $apiKeys[array_rand($apiKeys)];
        config(['openai.api_key' => $apiKey]);
        //$this->client = FacadesOpenAI::client($this->settings->openai_api_secret);
    }
    public function index()
    {
        $user = Auth::user();
        $nextLegs = TheNextLeg::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $resultHTML = "";
        foreach ($nextLegs as $thenextleg) {

            $thenextlegImages = TheNextlegImages::where(['thenextleg_id' => $thenextleg->id, 'variation_of' => null])->orderBy('created_at', 'desc')->get();

            foreach ($thenextlegImages as $variation) {
                $thenextlegImages1 = TheNextlegImages::where('variation_of', $variation->id)->orderBy('created_at', 'desc')->get();
                if (count($thenextlegImages1) > 0) {
                    $resultHTML .= view('partial_result_box_magiclensai', ["thenextlegImages" => $thenextlegImages1]);
                }
            }

            if (count($thenextlegImages) > 0) {
                $resultHTML .= view('partial_result_box_magiclensai', compact('thenextlegImages'));
            }
        }
        return view('magiclensai', compact('resultHTML'));
    }

    public function generateAIRequest(Request $request, MagicLensAIService $theNextLegService)
    {
        $badWordsFilterResult = $this->badWordsFilter($request, $theNextLegService);
        $data = $badWordsFilterResult->getData();
        if ($data->isNaughty === true) {
            $data = array(
                'errors' => ['You are not allowed to use this word.'],
            );
            return response()->json($data, 400);
        }
        $data = $theNextLegService->imagine($request);
        $content = $data->getContent();
        $responseData = json_decode($content, true);
        if (isset($responseData['success']) && $responseData['success'] === true) {
            $user = Auth::user();

            if ($user->remaining_images == 0) {
                $data = array(
                    'errors' => ['You have no credits left. Please consider upgrading your plan.'],
                );
                return response()->json($data, 419);
            }

            $entry = new TheNextleg();
            $entry->user_id = Auth::id();
            $entry->request = $request->msg;
            $entry->imagine_api_response = json_encode($responseData);
            $entry->credits = 0;
            $entry->save();
        }
        return $data;
    }

    public function checkAIProgress(MagicLensAIService $theNextLegService, Request $request, $messageId)
    {
        $id = $request['id'];
        $loadBalanceId = $request['loadBalanceId'];
        $user = Auth::user();
        $data = $theNextLegService->message($messageId, $loadBalanceId);
        $content = $data->getContent();
        $responseData = json_decode($content, true);
        if (isset($responseData['progress']) && $responseData['progress'] <= 100) {
            $thenextlegs = Thenextleg::orderBy('created_at', 'desc')->take(1)->first();
            $entry = new TheNextLegProgressLog();
            $entry->nextleg_id = $thenextlegs->id;
            $entry->progress_response = json_encode($responseData);
            $entry->save();

            if (isset($responseData['progress']) && $responseData['progress'] == 100) {
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
            $thenextlegImagesArray = [];
            $count = 1;
            if (isset($responseData['response']['imageUrls']) && !empty($responseData['response']['imageUrls'])) {
                $thenextlegs = Thenextleg::orderBy('created_at', 'desc')->take(1)->first();
                foreach ($responseData['response']['imageUrls'] as $key => $imageUrl) {
                    $thenextlegImages = new TheNextLegImages();
                    $thenextlegImages->thenextleg_id = $thenextlegs->id;
                    $thenextlegImages->image_path = $imageUrl;
                    $thenextlegImages->image_index = $count++;
                    $thenextlegImages->save();

                    if (isset($responseData['response']['type']) && $responseData['response']['type'] == 'button') {
                        $thenextlegImages->variation_of = $id;
                        $thenextlegImages->save();
                    }

                    $image = $theNextLegService->getImage($imageUrl);

                    $filename = uniqid('image_') . $key . '.png';
                    Storage::disk('public')->put($filename, $image);
                    
                    $image_storage = $this->settings_two->ai_image_storage;
                    if ($image_storage == self::STORAGE_S3) {
                        $path = 'uploads/' .$filename;
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        $path = Storage::disk('s3')->url($aws_path);
                    }

                    $thenextlegImages->image_local_path = 'uploads/' . $filename;
                    $thenextlegImages->aws_path = $path;
                    $thenextlegImages->save();

                    $thenextlegImagesArray[$key]['images'] = $thenextlegImages;
                    $thenextlegImagesArray[$key]['button_message_id'] = $responseData['response']['buttonMessageId'];
                    $thenextlegImagesArray[$key]['image_index'] = $thenextlegImages->image_index;
                }
            }
            $data = view('append_images', ['thenextlegImagesArrays' => $thenextlegImagesArray])->render();
        } else {
            $data = array(
                'errors' => ['Your Progress is Incomplete.'],
            );
        }

        return response()->json(['data' => $data, 'progress' => $responseData['progress']]);
    }

    public function imageDelete($id)
    {
        $image = TheNextLegImages::findOrFail($id);
        if (!empty($image)) {
            $image->delete();
            return back()->with(['message' => 'Deleted successfully', 'type' => 'success']);
        } else {
            return back()->with(['message' => 'Image not found', 'type' => 'error']);
        }
    }

    public function upscaleImages(Request $request, MagicLensAIService $theNextLegService)
    {
        return  $theNextLegService->button($request);
    }
    public function badWordsFilter(Request $request, MagicLensAIService $theNextLegService)
    {
        return $theNextLegService->badWords($request);
    }
}
