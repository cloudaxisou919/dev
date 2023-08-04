<?php

use App\Http\Controllers\PaymentController;

use App\Models\Activity;
use App\Models\Gateways;
use App\Models\Setting;
use App\Models\Subscriptions;
use App\Models\YokassaSubscriptions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\UserUpvote;
use App\Models\User;
use App\Models\UserCategory;


function activeRoute($route_name){
    if (Route::currentRouteName() == $route_name){
        return 'active';
    }
}

function activeRouteBulk($route_names){
    $current_route = Route::currentRouteName();
    if (in_array($current_route, $route_names)){
        return 'active';
    }
}

function activeRouteBulkShow($route_names){
    $current_route = Route::currentRouteName();
    if (in_array($current_route, $route_names)){
        return 'show';
    }
}


function createActivity($user_id, $activity_type, $activity_title, $url){
    $activityEntry = new Activity();
    $activityEntry->user_id = $user_id;
    $activityEntry->activity_type = $activity_type;
    $activityEntry->activity_title = $activity_title;
    $activityEntry->url = $url;
    $activityEntry->save();

}

function percentageChange($old, $new, int $precision = 1){
    if ($old == 0) {
        $old++;
        $new++;
    }
    $change = round((($new - $old) / $old) * 100, $precision);

    if ($change < 0 ){
        return '<span class="inline-flex items-center leading-none !ms-2 text-[var(--tblr-red)] text-[10px] bg-[rgba(var(--tblr-red-rgb),0.15)] px-[5px] py-[3px] rounded-[3px]">
            <svg class="mr-1 -scale-100" width="7" height="4" viewBox="0 0 7 4" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 3.2768C0 3.32591 0.0245541 3.38116 0.061384 3.41799L0.368304 3.72491C0.405134 3.76174 0.46038 3.78629 0.509487 3.78629C0.558594 3.78629 0.61384 3.76174 0.65067 3.72491L3.06306 1.31252L5.47545 3.72491C5.51228 3.76174 5.56752 3.78629 5.61663 3.78629C5.67188 3.78629 5.72098 3.76174 5.75781 3.72491L6.06473 3.41799C6.10156 3.38116 6.12612 3.32591 6.12612 3.2768C6.12612 3.2277 6.10156 3.17245 6.06473 3.13562L3.20424 0.275129C3.16741 0.238299 3.11217 0.213745 3.06306 0.213745C3.01395 0.213745 2.95871 0.238299 2.92188 0.275129L0.061384 3.13562C0.0245541 3.17245 0 3.2277 0 3.2768Z"/>
            </svg>
            '.$change.'%
        </span>';
    }else{
        return '<span class="inline-flex items-center leading-none !ms-2 text-[var(--tblr-green)] text-[10px] bg-[rgba(var(--tblr-green-rgb),0.15)] px-[5px] py-[3px] rounded-[3px]">
                    <svg class="mr-1" width="7" height="4" viewBox="0 0 7 4" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 3.2768C0 3.32591 0.0245541 3.38116 0.061384 3.41799L0.368304 3.72491C0.405134 3.76174 0.46038 3.78629 0.509487 3.78629C0.558594 3.78629 0.61384 3.76174 0.65067 3.72491L3.06306 1.31252L5.47545 3.72491C5.51228 3.76174 5.56752 3.78629 5.61663 3.78629C5.67188 3.78629 5.72098 3.76174 5.75781 3.72491L6.06473 3.41799C6.10156 3.38116 6.12612 3.32591 6.12612 3.2768C6.12612 3.2277 6.10156 3.17245 6.06473 3.13562L3.20424 0.275129C3.16741 0.238299 3.11217 0.213745 3.06306 0.213745C3.01395 0.213745 2.95871 0.238299 2.92188 0.275129L0.061384 3.13562C0.0245541 3.17245 0 3.2277 0 3.2768Z"/>
                    </svg>
                    '.$change.'%
                </span>';
    }


}

function percentageChangeSign($old, $new, int $precision = 2){

    if (percentageChange($old, $new) > 0){
        return 'plus';
    }else{
        return 'minus';
    }

}


function currency(){
    $setting = \App\Models\Setting::first();
    return \App\Models\Currency::where('id', $setting->default_currency)->first();
}

function getSubscription(){
    $userId=Auth::user()->id;
    $activeSub = Subscriptions::where([['stripe_status', '=', 'active'], ['user_id', '=', $userId]])->orWhere([['stripe_status', '=', 'trialing'], ['user_id', '=', $userId]])->first();
    if($activeSub == null) {
        $activeSub = YokassaSubscriptions::where([['subscription_status', '=', 'active'], ['user_id', '=', $userId]])->first();
    }
    return $activeSub;
}

function getSubscriptionActive(){
    return getSubscription();
}

function getSubscriptionStatus(){
    return PaymentController::getSubscriptionStatus();
}

function checkIfTrial(){
    return PaymentController::checkIfTrial();
}

function getSubscriptionName(){
    $user = Auth::user();
    return \App\Models\PaymentPlans::where('id', getSubscription()->name)->first()->name;
}

function getYokassaSubscriptionName(){
    $user = Auth::user();
    return \App\Models\PaymentPlans::where('id', getYokassaSubscription()->plan_id)->first()->plan_id;
}

function getSubscriptionRenewDate()
{
    return PaymentController::getSubscriptionRenewDate();
}

function getSubscriptionDaysLeft()
{
    return PaymentController::getSubscriptionDaysLeft();
}

//Templates favorited
function isFavorited($template_id){
    $isFav = \App\Models\UserFavorite::where('user_id', Auth::id())->where('openai_id', $template_id)->exists();
    return $isFav;
}

//Country Flags
function country2flag(string $countryCode): string
{

    if (strpos($countryCode, '-') !== false) {
        $countryCode = substr($countryCode, strpos($countryCode, '-') + 1);
    } elseif (strpos($countryCode, '_') !== false) {
        $countryCode = substr($countryCode, strpos($countryCode, '_') + 1);
    }

    if ( $countryCode === 'el' ){
        $countryCode = 'gr';
    }elseif ( $countryCode === 'da' ){
        $countryCode = 'dk';
    }
    
    return (string) preg_replace_callback(
        '/./',
        static fn (array $letter) => mb_chr(ord($letter[0]) % 32 + 0x1F1E5),
        $countryCode
    );
}

//Memory Limit
function getServerMemoryLimit() {
    return (int) ini_get('memory_limit');
}

//Count Words
function countWords($text){

    $encoding = mb_detect_encoding($text);

    if ($encoding === 'UTF-8') {
        // Count Chinese words by splitting the string into individual characters
        $words = preg_match_all('/\p{Han}|\p{L}+|\p{N}+/u', $text);
    } else {
        // For other languages, use str_word_count()
        $words = str_word_count($text, 0, $encoding);
    }

    return (int)$words;

}

function getDefinedLangs() {
    $fields = \DB::connection('locations')->getSchemaBuilder()->getColumnListing('strings');
    $exceptions = ['en','code','created_at','updated_at'];
    $filtered = collect($fields)->filter(function ($value, $key) use($exceptions){
        if (!in_array($value,$exceptions) ) {
            return $value;
        }
    });
    return $filtered->all();
}

function getVoiceNames($hash) {
    $voiceNames =[
        "af-ZA-Standard-A" => "Ayanda (Female)",
        "ar-XA-Standard-A" => "Fatima (Female)",
        "ar-XA-Standard-B" => "Ahmed (Male)",
        "ar-XA-Standard-C" => "Mohammed (Male)",
        "ar-XA-Standard-D" => "Aisha (Female)",
        "ar-XA-Wavenet-A" => "Layla (Female)",
        "ar-XA-Wavenet-B" => "Ali (Male)",
        "ar-XA-Wavenet-C" => "Omar (Male)",
        "ar-XA-Wavenet-D" => "Zahra (Female)",
        "eu-ES-Standard-A" => "Ane (Female)",
        "bn-IN-Standard-A" => "Ananya (Female)",
        "bn-IN-Standard-B" => "Aryan (Male)",
        "bn-IN-Wavenet-A" => "Ishita (Female)",
        "bn-IN-Wavenet-B" => "Arry (Male)",
        "bg-BG-Standard-A" => "Elena (Female)",
        "ca-ES-Standard-A" => "Laia (Female)",
        "yue-HK-Standard-A" => "Wing (Female)",
        "yue-HK-Standard-B" => "Ho (Male)",
        "yue-HK-Standard-C" => "Siu (Female)",
        "yue-HK-Standard-D" => "Lau (Male)",
        "cs-CZ-Standard-A" => "Tereza (Female)",
        "cs-CZ-Wavenet-A" => "Karolína (Female)",
        //"da-DK-Neural2-D" => "Neural2 - FEMALE",
        //"da-DK-Neural2-F" => "Neural2 - MALE",                    
        "da-DK-Standard-A" => "Emma (Female)",
        "da-DK-Standard-A" => "Freja (Female)",
        "da-DK-Standard-A" => "Ida (Female)",
        "da-DK-Standard-C" => "Noah (Male)",
        "da-DK-Standard-D" => "Mathilde (Female)",
        "da-DK-Standard-E" => "Clara (Female)",
        "da-DK-Wavenet-A" => "Isabella (Female)",
        "da-DK-Wavenet-C" => "Lucas (Male)",
        "da-DK-Wavenet-D" => "Olivia (Female)",
        "da-DK-Wavenet-E" => "Emily (Female)",
        "nl-BE-Standard-A" => "Emma (Female)",
        "nl-BE-Standard-B" => "Thomas (Male)",
        "nl-BE-Wavenet-A" => "Sophie (Female)",
        "nl-BE-Wavenet-B" => "Lucas (Male)",
        "nl-NL-Standard-A" => "Emma (Female)",
        "nl-NL-Standard-B" => "Daan (Male)",
        "nl-NL-Standard-C" => "Luuk (Male)",
        "nl-NL-Standard-D" => "Lotte (Female)",
        "nl-NL-Standard-E" => "Sophie (Female)",
        "nl-NL-Wavenet-A" => "Mila (Female)",
        "nl-NL-Wavenet-B" => "Sem (Male)",
        "nl-NL-Wavenet-C" => "Stijn (Male)",
        "nl-NL-Wavenet-D" => "Fenna (Female)",
        "nl-NL-Wavenet-E" => "Eva (Female)",
        //"en-AU-Neural2-A" => "Neural2 - FEMALE",
        //"en-AU-Neural2-B" => "Neural2 - MALE",
        //"en-AU-Neural2-C" => "Neural2 - FEMALE",
        //"en-AU-Neural2-D" => "Neural2 - MALE",
        "en-AU-News-E" => "Emma (Female)",
        "en-AU-News-F" => "Olivia (Female)",
        "en-AU-News-G" => "Liam (Male)",
        "en-AU-Polyglot-1" => "Noah (Male)",
        "en-AU-Standard-A" => "Charlotte (Female)",
        "en-AU-Standard-B" => "Oliver (Male)",
        "en-AU-Standard-C" => "Ava (Female)",
        "en-AU-Standard-D" => "Jack (Male)",
        "en-AU-Wavenet-A" => "Sophie (Female)",
        "en-AU-Wavenet-B" => "William (Male)",
        "en-AU-Wavenet-C" => "Amelia (Female)",
        "en-AU-Wavenet-D" => "Thomas (Male)",
        "en-IN-Standard-A" => "Aditi (Female)",
        "en-IN-Standard-B" => "Arjun (Male)",
        "en-IN-Standard-C" => "Rohan (Male)",
        "en-IN-Standard-D" => "Ananya (Female)",
        "en-IN-Wavenet-A" => "Alisha (Female)",
        "en-IN-Wavenet-B" => "Aryan (Male)",
        "en-IN-Wavenet-C" => "Kabir (Male)",
        "en-IN-Wavenet-D" => "Diya (Female)",
        //"en-GB-Neural2-A" => "Neural2 - FEMALE",
        //"en-GB-Neural2-B" => "Neural2 - MALE",
        //"en-GB-Neural2-C" => "Neural2 - FEMALE",
        //"en-GB-Neural2-D" => "Neural2 - MALE",
        //"en-GB-Neural2-F" => "Neural2 - FEMALE",
        "en-GB-News-G" => "Amelia (Female)",
        "en-GB-News-H" => "Elise (Female)",
        "en-GB-News-I" => "Isabella (Female)",
        "en-GB-News-J" => "Jessica (Female)",
        "en-GB-News-K" => "Alexander (Male)",
        "en-GB-News-L" => "Benjamin (Male)",
        "en-GB-News-M" => "Charles (Male)",
        "en-GB-Standard-A" => "Emily (Female)",
        "en-GB-Standard-B" => "John (Male)",
        "en-GB-Standard-C" => "Mary (Female)",
        "en-GB-Standard-D" => "Peter (Male)",
        "en-GB-Standard-F" => "Sarah (Female)",
        "en-GB-Wavenet-A" => "Ava (Female)",
        "en-GB-Wavenet-B" => "David (Male)",
        "en-GB-Wavenet-C" => "Emily (Female)",
        "en-GB-Wavenet-D" => "James (Male)",
        "en-GB-Wavenet-F" => "Sophie (Female)",
        //"en-US-Neural2-A" => "Neural2 - MALE",
        //"en-US-Neural2-C" => "Neural2 - FEMALE",
        //"en-US-Neural2-D" => "Neural2 - MALE",
        //"en-US-Neural2-E" => "Neural2 - FEMALE",
        //"en-US-Neural2-F" => "Neural2 - FEMALE",
        //"en-US-Neural2-G" => "Neural2 - FEMALE",
        //"en-US-Neural2-H" => "Neural2 - FEMALE",
        //"en-US-Neural2-I" => "Neural2 - MALE",
        //"en-US-Neural2-J" => "Neural2 - MALE",
        "en-US-News-K" => "Lily (Female)",
        "en-US-News-L" => "Olivia (Female)",
        "en-US-News-M" => "Noah (Male)",
        "en-US-News-N" => "Oliver (Male)",
        "en-US-Polyglot-1" => "John (Male)",
        "en-US-Standard-A" => "Michael (Male)",
        "en-US-Standard-B" => "David (Male)",
        "en-US-Standard-C" => "Emma (Female)",
        "en-US-Standard-D" => "William (Male)",
        "en-US-Standard-E" => "Ava (Female)",
        "en-US-Standard-F" => "Sophia (Female)",
        "en-US-Standard-G" => "Isabella (Female)",
        "en-US-Standard-H" => "Charlotte (Female)",
        "en-US-Standard-I" => "James (Male)",
        "en-US-Standard-J" => "Lucas (Male)",
        "en-US-Studio-M" => "Benjamin (Male)",
        "en-US-Studio-O" => "Eleanor (Female)",
        "en-US-Wavenet-A" => "Alexander (Male)",
        "en-US-Wavenet-B" => "Benjamin (Male)",
        "en-US-Wavenet-C" => "Emily (Female)",
        "en-US-Wavenet-D" => "James (Male)",
        "en-US-Wavenet-E" => "Ava (Female)",
        "en-US-Wavenet-F" => "Sophia (Female)",
        "en-US-Wavenet-G" => "Isabella (Female)",
        "en-US-Wavenet-H" => "Charlotte (Female)",
        "en-US-Wavenet-I" => "Alexander (Male)",
        "en-US-Wavenet-J" => "Lucas (Male)",
        "fil-PH-Standard-A" => "Maria (Female)",
        "fil-PH-Standard-B" => "Juana (Female)",
        "fil-PH-Standard-C" => "Juan (Male)",
        "fil-PH-Standard-D" => "Pedro (Male)",
        "fil-PH-Wavenet-A" => "Maria (Female)",
        "fil-PH-Wavenet-B" => "Juana (Female)",
        "fil-PH-Wavenet-C" => "Juan (Male)",
        "fil-PH-Wavenet-D" => "Pedro (Male)",
        //"fil-ph-Neural2-A" => "Neural2 - FEMALE",
        //"fil-ph-Neural2-D" => "Neural2 - MALE",
        "fi-FI-Standard-A" => "Sofia (Female)",
        "fi-FI-Wavenet-A" => "Sofianna (Female)",
        //"fr-CA-Neural2-A" => "Neural2 - FEMALE",
        //"fr-CA-Neural2-B" => "Neural2 - MALE",
        //"fr-CA-Neural2-C" => "Neural2 - FEMALE",
        //"fr-CA-Neural2-D" => "Neural2 - MALE",
        "fr-CA-Standard-A" => "Emma (Female)",
        "fr-CA-Standard-B" => "Jean (Male)",
        "fr-CA-Standard-C" => "Gabrielle (Female)",
        "fr-CA-Standard-D" => "Thomas (Male)",
        "fr-CA-Wavenet-A" => "Amelie (Female)",
        "fr-CA-Wavenet-B" => "Antoine (Male)",
        "fr-CA-Wavenet-C" => "Gabrielle (Female)",
        "fr-CA-Wavenet-D" => "Thomas (Male)",
        //"fr-FR-Neural2-A" => "Neural2 - FEMALE",
        //"fr-FR-Neural2-B" => "Neural2 - MALE",
        //"fr-FR-Neural2-C" => "Neural2 - FEMALE",
        //"fr-FR-Neural2-D" => "Neural2 - MALE",
        //"fr-FR-Neural2-E" => "Neural2 - FEMALE",
        "fr-FR-Polyglot-1" => "Jean (Male)",
        "fr-FR-Standard-A" => "Marie (Female)",
        "fr-FR-Standard-B" => "Pierre (Male)",
        "fr-FR-Standard-C" => "Sophie (Female)",
        "fr-FR-Standard-D" => "Paul (Male)",
        "fr-FR-Standard-E" => "Julie (Female)",
        "fr-FR-Wavenet-A" => "Elise (Female)",
        "fr-FR-Wavenet-B" => "Nicolas (Male)",
        "fr-FR-Wavenet-C" => "Clara (Female)",
        "fr-FR-Wavenet-D" => "Antoine (Male)",
        "fr-FR-Wavenet-E" => "Amelie (Female)",
        "gl-ES-Standard-A" => "Ana (Female)",
        //"de-DE-Neural2-B" => "Neural2 - MALE",
        //"de-DE-Neural2-C" => "Neural2 - FEMALE",
        //"de-DE-Neural2-D" => "Neural2 - MALE",
        //"de-DE-Neural2-F" => "Neural2 - FEMALE",
        "de-DE-Polyglot-1" => "Johannes (Male)",
        "de-DE-Standard-A" => "Anna (Female)",
        "de-DE-Standard-B" => "Max (Male)",
        "de-DE-Standard-C" => "Sophia (Female)",
        "de-DE-Standard-D" => "Paul (Male)",
        "de-DE-Standard-E" => "Erik (Male)",
        "de-DE-Standard-F" => "Lina (Female)",
        "de-DE-Wavenet-A" => "Eva (Female)",
        "de-DE-Wavenet-B" => "Felix (Male)",
        "de-DE-Wavenet-C" => "Emma (Female)",
        "de-DE-Wavenet-D" => "Lukas (Male)",
        "de-DE-Wavenet-E" => "Nico (Male)",
        "de-DE-Wavenet-F" => "Mia (Female)",
        "el-GR-Standard-A" => "Ελένη (Female)",
        "el-GR-Wavenet-A" => "Ελένη (Female)",
        "gu-IN-Standard-A" => "દિવ્યા (Female)",
        "gu-IN-Standard-B" => "કિશોર (Male)",
        "gu-IN-Wavenet-A" => "દિવ્યા (Female)",
        "gu-IN-Wavenet-B" => "કિશોર (Male)",
        "he-IL-Standard-A" => "Tamar (Female)",
        "he-IL-Standard-B" => "David (Male)",
        "he-IL-Standard-C" => "Michal (Female)",
        "he-IL-Standard-D" => "Jonathan (Male)",
        "he-IL-Wavenet-A" => "Yael (Female)",
        "he-IL-Wavenet-B" => "Eli (Male)",
        "he-IL-Wavenet-C" => "Abigail (Female)",
        "he-IL-Wavenet-D" => "Alex (Male)",
        //"hi-IN-Neural2-A" => "Neural2 - FEMALE",
        //"hi-IN-Neural2-B" => "Neural2 - MALE",
        //"hi-IN-Neural2-C" => "Neural2 - MALE",
        //"hi-IN-Neural2-D" => "Neural2 - FEMALE",
        "hi-IN-Standard-A" => "Aditi (Female)",
        "hi-IN-Standard-B" => "Abhishek (Male)",
        "hi-IN-Standard-C" => "Aditya (Male)",
        "hi-IN-Standard-D" => "Anjali (Female)",
        "hi-IN-Wavenet-A" => "Kiara (Female)",
        "hi-IN-Wavenet-B" => "Rohan (Male)",
        "hi-IN-Wavenet-C" => "Rishabh (Male)",
        "hi-IN-Wavenet-D" => "Srishti (Female)",
        "hu-HU-Standard-A" => "Eszter (Female)",
        "hu-HU-Wavenet-A" => "Lilla (Female)",
        "is-IS-Standard-A" => "Guðrún (Female)",
        "id-ID-Standard-A" => "Amelia (Female)",
        "id-ID-Standard-B" => "Fajar (Male)",
        "id-ID-Standard-C" => "Galih (Male)",
        "id-ID-Standard-D" => "Kiara (Female)",
        "id-ID-Wavenet-A" => "Nadia (Female)",
        "id-ID-Wavenet-B" => "Reza (Male)",
        "id-ID-Wavenet-C" => "Satria (Male)",
        "id-ID-Wavenet-D" => "Vania (Female)",
        //"it-IT-Neural2-A" => "Neural2 - FEMALE",
        //"it-IT-Neural2-C" => "Neural2 - MALE",
        "it-IT-Standard-A" => "Chiara (Female)",
        "it-IT-Standard-B" => "Elisa (Female)",
        "it-IT-Standard-C" => "Matteo (Male)",
        "it-IT-Standard-D" => "Riccardo (Male)",
        "it-IT-Wavenet-A" => "Valentina (Female)",
        "it-IT-Wavenet-B" => "Vittoria (Female)",
        "it-IT-Wavenet-C" => "Andrea (Male)",
        "it-IT-Wavenet-D" => "Luca (Male)",
        //"ja-JP-Neural2-B" => "Neural2 - FEMALE",
        //"ja-JP-Neural2-C" => "Neural2 - MALE",
        //"ja-JP-Neural2-D" => "Neural2 - MALE",
        "ja-JP-Standard-A" => "Akane (Female)",
        "ja-JP-Standard-B" => "Emi (Female)",
        "ja-JP-Standard-C" => "Daisuke (Male)",
        "ja-JP-Standard-D" => "Kento (Male)",
        "ja-JP-Wavenet-A" => "Haruka (Female)",
        "ja-JP-Wavenet-B" => "Rin (Female)",
        "ja-JP-Wavenet-C" => "Shun (Male)",
        "ja-JP-Wavenet-D" => "Yuta (Male)",
        "kn-IN-Standard-A" => "Dhanya (Female)",
        "kn-IN-Standard-B" => "Keerthi (Male)",
        "kn-IN-Wavenet-A" => "Meena (Female)",
        "kn-IN-Wavenet-B" => "Nandini (Male)",
        //"ko-KR-Neural2-A" => "Neural2 - FEMALE",
        //"ko-KR-Neural2-B" => "Neural2 - FEMALE",
        //"ko-KR-Neural2-C" => "Neural2 - MALE",
        "ko-KR-Standard-A" => "So-young (Female)",
        "ko-KR-Standard-B" => "Se-yeon (Female)",
        "ko-KR-Standard-C" => "Min-soo (Male)",
        "ko-KR-Standard-D" => "Seung-woo (Male)",
        "ko-KR-Wavenet-A" => "Ji-soo (Female)",
        "ko-KR-Wavenet-B" => "Yoon-a (Female)",
        "ko-KR-Wavenet-C" => "Tae-hyun (Male)",
        "ko-KR-Wavenet-D" => "Jun-ho (Male)",
        "lv-LV-Standard-A" => "Raivis (Male)",
        "lv-LT-Standard-A" => "Raivis (Male)",
        "ms-MY-Standard-A" => "Amira (Female)",
        "ms-MY-Standard-B" => "Danial (Male)",
        "ms-MY-Standard-C" => "Eira (Female)",
        "ms-MY-Standard-D" => "Farhan (Male)",
        "ms-MY-Wavenet-A" => "Hana (Female)",
        "ms-MY-Wavenet-B" => "Irfan (Male)",
        "ms-MY-Wavenet-C" => "Janna (Female)",
        "ms-MY-Wavenet-D" => "Khairul (Male)",
        "ml-IN-Standard-A" => "Aishwarya (Female)",
        "ml-IN-Standard-B" => "Dhruv (Male)",
        "ml-IN-Wavenet-A" => "Deepthi (Female)",
        "ml-IN-Wavenet-B" => "Gautam (Male)",
        "ml-IN-Wavenet-C" => "Isha (Female)",
        "ml-IN-Wavenet-D" => "Kabir (Male)",
        "cmn-CN-Standard-A" => "Xiaomei (Female)",
        "cmn-CN-Standard-B" => "Lijun (Male)",
        "cmn-CN-Standard-C" => "Minghao (Male)",
        "cmn-CN-Standard-D" => "Yingying (Female)",
        "cmn-CN-Wavenet-A" => "Shanshan (Female)",
        "cmn-CN-Wavenet-B" => "Chenchen (Male)",
        "cmn-CN-Wavenet-C" => "Jiahao (Male)",
        "cmn-CN-Wavenet-D" => "Yueyu (Female)",
        "cmn-TW-Standard-A" => "Jingwen (Female)",
        "cmn-TW-Standard-B" => "Jinghao (Male)",
        "cmn-TW-Standard-C" => "Tingting (Female)",
        "cmn-TW-Wavenet-A" => "Yunyun (Female)",
        "cmn-TW-Wavenet-B" => "Zhenghao (Male)",
        "cmn-TW-Wavenet-C" => "Yuehan (Female)",
        "mr-IN-Standard-A" => "Anjali (Female)",
        "mr-IN-Standard-B" => "Aditya (Male)",
        "mr-IN-Standard-C" => "Dipti (Female)",
        "mr-IN-Wavenet-A" => "Gauri (Female)",
        "mr-IN-Wavenet-B" => "Harsh (Male)",
        "mr-IN-Wavenet-C" => "Ishita (Female)",
        "nb-NO-Standard-A" => "Ingrid (Female)",
        "nb-NO-Standard-B" => "Jonas (Male)",
        "nb-NO-Standard-C" => "Marit (Female)",
        "nb-NO-Standard-D" => "Olav (Male)",
        "nb-NO-Standard-E" => "Silje (Female)",
        "nb-NO-Wavenet-A" => "Astrid (Female)",
        "nb-NO-Wavenet-B" => "Eirik (Male)",
        "nb-NO-Wavenet-C" => "Inger (Female)",
        "nb-NO-Wavenet-D" => "Kristian (Male)",
        "nb-NO-Wavenet-E" => "Trine (Female)",
        "pl-PL-Standard-A" => "Agata (Female)",
        "pl-PL-Standard-B" => "Bartosz (Male)",
        "pl-PL-Standard-C" => "Kamil (Male)",
        "pl-PL-Standard-D" => "Julia (Female)",
        "pl-PL-Standard-E" => "Magdalena (Female)",
        "pl-PL-Wavenet-A" => "Natalia (Female)",
        "pl-PL-Wavenet-B" => "Paweł (Male)",
        "pl-PL-Wavenet-C" => "Tomasz (Male)",
        "pl-PL-Wavenet-D" => "Zofia (Female)",
        "pl-PL-Wavenet-E" => "Wiktoria (Female)",
        //"pt-BR-Neural2-A" => "Neural2 - FEMALE",
        //"pt-BR-Neural2-B" => "Neural2 - MALE",
        //"pt-BR-Neural2-C" => "Neural2 - FEMALE",
        "pt-BR-Standard-A" => "Ana (Female)",
        "pt-BR-Standard-B" => "Carlos (Male)",
        "pt-BR-Standard-C" => "Maria (Female)",
        "pt-BR-Wavenet-A" => "Julia (Female)",
        "pt-BR-Wavenet-B" => "João (Male)",
        "pt-BR-Wavenet-C" => "Fernanda (Female)",
        "pt-PT-Standard-A" => "Maria (Female)",
        "pt-PT-Standard-B" => "José (Male)",
        "pt-PT-Standard-C" => "Luís (Male)",
        "pt-PT-Standard-D" => "Ana (Female)",
        "pt-PT-Wavenet-A" => "Catarina (Female)",
        "pt-PT-Wavenet-B" => "Miguel (Male)",
        "pt-PT-Wavenet-C" => "João (Male)",
        "pt-PT-Wavenet-D" => "Marta (Female)",
        "pa-IN-Standard-A" => "Harpreet (Female)",
        "pa-IN-Standard-B" => "Gurpreet (Male)",
        "pa-IN-Standard-C" => "Jasmine (Female)",
        "pa-IN-Standard-D" => "Rahul (Male)",
        "pa-IN-Wavenet-A" => "Simran (Female)",
        "pa-IN-Wavenet-B" => "Amardeep (Male)",
        "pa-IN-Wavenet-C" => "Kiran (Female)",
        "pa-IN-Wavenet-D" => "Raj (Male)",
        "ro-RO-Standard-A" => "Maria (Female)",
        "ro-RO-Wavenet-A" => "Ioana (Female)",
        "ru-RU-Standard-A" => "Anastasia",
        "ru-RU-Standard-B" => "Alexander",
        "ru-RU-Standard-C" => "Elizabeth",
        "ru-RU-Standard-D" => "Michael",
        "ru-RU-Standard-E" => "Victoria",
        "ru-RU-Wavenet-A" => "Daria",
        "ru-RU-Wavenet-B" => "Dmitry",
        "ru-RU-Wavenet-C" => "Kristina",
        "ru-RU-Wavenet-D" => "Ivan",
        "ru-RU-Wavenet-E" => "Sophia",
        "sr-RS-Standard-A" => "Ana",
        "sk-SK-Standard-A" => "Mária (Female)",
        "sk-SK-Wavenet-A" => "Zuzana (Female)",
        //"es-ES-Neural2-A" => "Neural2 - FEMALE",
        //"es-ES-Neural2-B" => "Neural2 - MALE",
        //"es-ES-Neural2-C" => "Neural2 - FEMALE",
        //"es-ES-Neural2-D" => "Neural2 - FEMALE",
        //"es-ES-Neural2-E" => "Neural2 - FEMALE",
        //"es-ES-Neural2-F" => "Neural2 - MALE",
        "es-ES-Polyglot-1" => "Juan (Male)",
        "es-ES-Standard-A" => "María (Female)",
        "es-ES-Standard-B" => "José (Male)",
        "es-ES-Standard-C" => "Ana (Female)",
        "es-ES-Standard-D" => "Isabel (Female)",
        "es-ES-Wavenet-B" => "Pedro (Male)",
        "es-ES-Wavenet-C" => "Laura (Female)",
        "es-ES-Wavenet-D" => "Julia (Female)",
        //"es-US-Neural2-A" => "Neural2 - FEMALE",
        //"es-US-Neural2-B" => "Neural2 - MALE",
        //"es-US-Neural2-C" => "Neural2 - MALE",
        "es-US-News-D" => "Diego (Male)",
        "es-US-News-E" => "Eduardo (Male)",
        "es-US-News-F" => "Fátima (Female)",
        "es-US-News-G" => "Gabriela (Female)",
        "es-US-Polyglot-1" => "Juan (Male)",
        "es-US-Standard-A" => "Ana (Female)",
        "es-US-Standard-B" => "José (Male)",
        "es-US-Standard-C" => "Carlos (Male)",
        "es-US-Studio-B" => "Miguel (Male)",
        "es-US-Wavenet-A" => "Laura (Female)",
        "es-US-Wavenet-B" => "Pedro (Male)",
        "es-US-Wavenet-C" => "Pablo (Male)",
        "sv-SE-Standard-A" => "Ebba (Female)",
        "sv-SE-Standard-B" => "Saga (Female)",
        "sv-SE-Standard-C" => "Linnea (Female)",
        "sv-SE-Standard-D" => "Erik (Male)",
        "sv-SE-Standard-E" => "Anton (Male)",
        "sv-SE-Wavenet-A" => "Astrid (Female)",
        "sv-SE-Wavenet-B" => "Elin (Female)",
        "sv-SE-Wavenet-C" => "Oskar (Male)",
        "sv-SE-Wavenet-D" => "Hanna (Female)",
        "sv-SE-Wavenet-E" => "Felix (Male)",
        "ta-IN-Standard-A" => "Anjali (Female)",
        "ta-IN-Standard-B" => "Karthik (Male)",
        "ta-IN-Standard-C" => "Priya (Female)",
        "ta-IN-Standard-D" => "Ravi (Male)",
        "ta-IN-Wavenet-A" => "Lakshmi (Female)",
        "ta-IN-Wavenet-B" => "Suresh (Male)",
        "ta-IN-Wavenet-C" => "Uma (Female)",
        "ta-IN-Wavenet-D" => "Venkatesh (Male)",
        "-IN-Standard-A" => "Anjali - (Female)",
        "-IN-Standard-B" => "Karthik - (Male)",
        //"th-TH-Neural2-C" => "Neural2 - FEMALE",
        "th-TH-Standard-A" => "Ariya - (Female)",
        "tr-TR-Standard-A" => "Ayşe (Female)",
        "tr-TR-Standard-B" => "Berk (Male)",
        "tr-TR-Standard-C" => "Cansu (Female)",
        "tr-TR-Standard-D" => "Deniz (Female)",
        "tr-TR-Standard-E" => "Emre (Male)",
        "tr-TR-Wavenet-A" => "Gül (Female)",
        "tr-TR-Wavenet-B" => "Mert (Male)",
        "tr-TR-Wavenet-C" => "Nilay (Female)",
        "tr-TR-Wavenet-D" => "Selin (Female)",
        "tr-TR-Wavenet-E" => "Tolga (Male)",
        "uk-UA-Standard-A" => "Anya - (Female)",
        "uk-UA-Wavenet-A" => "Dasha - (Female)",
        //"vi-VN-Neural2-A" => "Neural2 - FEMALE",
        //"vi-VN-Neural2-D" => "Neural2 - MALE",
        "vi-VN-Standard-A" => "Mai (Female)",
        "vi-VN-Standard-B" => "Nam (Male)",
        "vi-VN-Standard-C" => "Hoa (Female)",
        "vi-VN-Standard-D" => "Huy (Male)",
        "vi-VN-Wavenet-A" => "Lan (Female)",
        "vi-VN-Wavenet-B" => "Son (Male)",
        "vi-VN-Wavenet-C" => "Thao (Female)",
        "vi-VN-Wavenet-D" => "Tuan (Male)",
    ];

    return $voiceNames[$hash] ?? $hash;
}


function format_double($number) {
    $parts = explode('.', $number);

    if ( count($parts) == 1 ) {
        return $parts[0] . '.0';
    }

    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '';

    if (strlen($decimalPart) > 1) {
        $secondDecimalPart = substr($decimalPart, 1);
    } else {
        $secondDecimalPart = '0';
    }

    return $integerPart . '.' . $decimalPart[0] . '.' . $secondDecimalPart;
}
