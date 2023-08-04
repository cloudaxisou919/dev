<?php

use App\Http\Controllers\AdvertisController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Gateways\StripeController;
use App\Http\Controllers\Gateways\PaypalController;
use App\Http\Controllers\Gateways\YokassaController;
use App\Http\Controllers\Dashboard\SupportController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\SearchController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\App;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Carbon\Carbon;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EmailTemplatesController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\GoogleTTSController;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {

        Route::get('/', [UserController::class, 'redirect'])->name('index');

        //User Area
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');

            //Openai generator
            Route::prefix('openai')->name('openai.')->group(function () {
                Route::get('/', [UserController::class, 'openAIList'])->name('list')->middleware('hasTokens');
                Route::get('/favorite-openai', [UserController::class, 'openAIFavoritesList'])->name('list.favorites');
                Route::post('/favorite', [UserController::class, 'openAIFavorite']);
                //Generators
                Route::middleware('hasTokens')->group(function () {
                    Route::get('/generator/{slug}', [UserController::class, 'openAIGenerator'])->name('generator');
                    Route::get('/generator/{slug}/workbook', [UserController::class, 'openAIGeneratorWorkbook'])->name('generator.workbook');
                });

                //Generators Generate
                Route::post('/generate', [AIController::class, 'buildOutput']);
                Route::get('/generate', [AIController::class, 'streamedTextOutput']);
                Route::get('/generate/lazyload', [AIController::class, 'lazyLoadImage'])->name('lazyloadimage');

                Route::get('/stream', [AIController::class, 'stream'])->name('stream');

                //Low systems
                Route::post('/low/generate_save', [AIController::class, 'lowGenerateSave']);

                Route::post('/generate-speech', [GoogleTTSController::class, 'generateSpeech']);


                //Documents
                Route::prefix('documents')->name('documents.')->group(function () {
                    Route::get('/all', [UserController::class, 'documentsAll'])->name('all');
                    Route::get('/images', [UserController::class, 'documentsImages'])->name('images');
                    Route::get('/single/{slug}', [UserController::class, 'documentsSingle'])->name('single');
                    Route::get('/delete/{slug}', [UserController::class, 'documentsDelete'])->name('delete');
                    Route::get('/delete/image/{slug}', [UserController::class, 'documentsImageDelete'])->name('image.delete');
                    Route::post('/workbook-save', [UserController::class, 'openAIGeneratorWorkbookSave']);
                });


                Route::middleware('hasTokens')->group(function () {
                    Route::prefix('chat')->name('chat.')->group(function () {
                        Route::get('/ai-chat-list', [AIChatController::class, 'openAIChatList'])->name('list');
                        Route::get('/ai-chat/{slug}', [AIChatController::class, 'openAIChat'])->name('chat');
                        Route::get('/stream', [AIController::class, 'chatStream'])->name('stream');
                        Route::match(['get', 'post'], '/chat-send', [AIChatController::class, 'chatOutput']);
                        Route::post('/open-chat-area-container', [AIChatController::class, 'openChatAreaContainer']);
                        Route::post('/start-new-chat', [AIChatController::class, 'startNewChat']);
                        Route::post('/search', [AIChatController::class, 'search']);
                        Route::post('/delete-chat', [AIChatController::class, 'deleteChat']);
                        Route::post('/rename-chat', [AIChatController::class, 'renameChat']);

                        //Low systems
                        Route::post('/low/chat_save', [AIChatController::class, 'lowChatSave']);
                    });
                });
            });

            // user profile settings
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [UserController::class, 'userSettings'])->name('index');
                Route::post('/save', [UserController::class, 'userSettingsSave']);
            });

            // Subscription and payment
            Route::prefix('payment')->name('payment.')->group(function () {
                Route::get('/', [UserController::class, 'subscriptionPlans'])->name('subscription');

                Route::get('/subscribe/{id}', [UserController::class, 'subscriptionPayment'])->name('subscription.payment');
                Route::post('/subscription/pay', [UserController::class, 'subscriptionPaymentPay'])->name('subscription.payment.pay');
                Route::get('/subscription/cancel', [UserController::class, 'subscriptionCancel'])->name('subscription.payment.cancel');
                Route::get('/prepaid/{id}', [UserController::class, 'prepaidPayment'])->name('prepaid.payment');
                Route::post('prepaid-payment//pay', [UserController::class, 'prepaidPaymentPay'])->name('prepaid.payment.pay');

                Route::get('/subscribe/{planId}/{gatewayCode}', [PaymentController::class, 'startSubscriptionProcess'])->name('startSubscriptionProcess');
                Route::get('/prepaid/{planId}/{gatewayCode}', [PaymentController::class, 'startPrepaidPaymentProcess'])->name('startPrepaidPaymentProcess');
                Route::get('/subscribe-cancel', [PaymentController::class, 'cancelActiveSubscription'])->name('cancelActiveSubscription');

                Route::post('/stripe/subscribePay', [StripeController::class, 'subscribePay'])->name('stripeSubscribePay');
                Route::post('/stripe/prepaidPay', [StripeController::class, 'prepaidPay'])->name('stripePrepaidPay');
                Route::post('/yokassa/subscribePay', [YokassaController::class, 'subscribePay'])->name('YokassaSubscribePay');
                Route::post('/yokassa/prepaidPay', [YokassaController::class, 'prepaidPay'])->name('YokassaPrepaidPay');

                Route::post('/paypal/create-paypal-order', [PaypalController::class, 'createPayPalOrder'])->name('createPayPalOrder');
                Route::post('/paypal/capture-paypal-order', [PaypalController::class, 'capturePayPalOrder'])->name('capturePayPalOrder');
                Route::post('/paypal/approve-paypal-subscription', [PaypalController::class, 'approvePaypalSubscription'])->name('approvePaypalSubscription');


                // Route::get('/subscribe-plan/{planId}', [PaymentController::class, 'startSubscriptionProcess'])->name('startSubscriptionProcess');
            });

            //Orders invoice billing
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [UserController::class, 'invoiceList'])->name('index');
                Route::get('/order/{order_id}', [UserController::class, 'invoiceSingle'])->name('invoice');
            });

            //Affiliates
            Route::prefix('affiliates')->name('affiliates.')->group(function () {
                Route::get('/', [UserController::class, 'affiliatesList'])->name('index');
                Route::post('/send-invitation', [UserController::class, 'affiliatesListSendInvitation']);
                Route::post('/send-request', [UserController::class, 'affiliatesListSendRequest']);
            });
        });


        //Admin Area
        Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');

            //User Management
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [AdminController::class, 'users'])->name('index');
                Route::get('/edit/{id}', [AdminController::class, 'usersEdit'])->name('edit');
                Route::get('/delete/{id}', [AdminController::class, 'usersDelete'])->name('delete');
                Route::post('/save', [AdminController::class, 'usersSave']);
            });

            //Openai management
            Route::prefix('openai')->name('openai.')->group(function () {
                Route::get('/', [AdminController::class, 'openAIList'])->name('list');
                Route::post('/update-status', [AdminController::class, 'openAIListUpdateStatus']);
                Route::post('/update-package-status', [AdminController::class, 'openAIListUpdatePackageStatus']);

                Route::prefix('custom')->name('custom.')->group(function () {
                    Route::get('/', [AdminController::class, 'openAICustomList'])->name('list');
                    Route::get('/add-or-update/{id?}', [AdminController::class, 'openAICustomAddOrUpdate'])->name('addOrUpdate');
                    Route::get('/delete/{id?}', [AdminController::class, 'openAICustomDelete'])->name('delete');
                    Route::post('/save', [AdminController::class, 'openAICustomAddOrUpdateSave']);
                });

                Route::prefix('categories')->name('categories.')->group(function () {
                    Route::get('/', [AdminController::class, 'openAICategoriesList'])->name('list');
                    Route::get('/add-or-update/{id?}', [AdminController::class, 'openAICategoriesAddOrUpdate'])->name('addOrUpdate');
                    Route::get('/delete/{id?}', [AdminController::class, 'openAICategoriesDelete'])->name('delete');
                    Route::post('/save', [AdminController::class, 'openAICategoriesAddOrUpdateSave']);
                });

                Route::prefix('chat')->name('chat.')->group(function () {
                    Route::get('/', [AdminController::class, 'openAIChatList'])->name('list');
                    Route::get('/add-or-update/{id?}', [AdminController::class, 'openAIChatAddOrUpdate'])->name('addOrUpdate');
                    Route::get('/delete/{id?}', [AdminController::class, 'openAIChatDelete'])->name('delete');
                    Route::post('/save', [AdminController::class, 'openAIChatAddOrUpdateSave']);
                });
            });

            //Finance
            Route::prefix('finance')->name('finance.')->group(function () {
                //Plans
                Route::prefix('plans')->name('plans.')->group(function () {
                    Route::get('/', [AdminController::class, 'paymentPlans'])->name('index');
                    Route::get('/subscription/create-or-update/{id?}', [AdminController::class, 'paymentPlansSubscriptionNewOrEdit'])->name('SubscriptionNewOrEdit');
                    Route::get('/pre-paid/create-or-update/{id?}', [AdminController::class, 'paymentPlansPrepaidNewOrEdit'])->name('PlanNewOrEdit');
                    Route::get('/delete/{id}', [AdminController::class, 'paymentPlansDelete'])->name('delete');
                    Route::post('/save', [AdminController::class, 'paymentPlansSave']);
                });

                //Payment Gateways
                Route::prefix('paymentGateways')->name('paymentGateways.')->group(function () {
                    Route::get('/', [GatewayController::class, 'paymentGateways'])->name('index');
                    Route::get('/settings/{code}', [GatewayController::class, 'gatewaySettings'])->name('settings');
                    Route::post('/settings/{code}/save', [GatewayController::class, 'gatewaySettingsSave'])->name('save');
                });
            });

            //Testimonials
            Route::prefix('testimonials')->name('testimonials.')->group(function () {
                Route::get('/', [AdminController::class, 'testimonials'])->name('index');
                Route::get('/create-or-update/{id?}', [AdminController::class, 'testimonialsNewOrEdit'])->name('TestimonialsNewOrEdit');
                Route::get('/delete/{id}', [AdminController::class, 'testimonialsDelete'])->name('delete');
                Route::post('/save', [AdminController::class, 'testimonialsSave']);
            });

            //Clients
            Route::prefix('clients')->name('clients.')->group(function () {
                Route::get('/', [AdminController::class, 'clients'])->name('index');
                Route::get('/create-or-update/{id?}', [AdminController::class, 'clientsNewOrEdit'])->name('ClientsNewOrEdit');
                Route::get('/delete/{id}', [AdminController::class, 'clientsDelete'])->name('delete');
                Route::post('/save', [AdminController::class, 'clientsSave']);
            });

            //How it Works
            Route::prefix('howitWorks')->name('howitWorks.')->group(function () {
                Route::get('/', [AdminController::class, 'howitWorks'])->name('index');
                Route::get('/create-or-update/{id?}', [AdminController::class, 'howitWorksNewOrEdit'])->name('HowitWorksNewOrEdit');
                Route::get('/delete/{id}', [AdminController::class, 'howitWorksDelete'])->name('delete');
                Route::post('/save', [AdminController::class, 'howitWorksSave']);
                Route::post('/bottom-line', [AdminController::class, 'howitWorksBottomLineSave']);
            });

            //Settings
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/general', [SettingsController::class, 'general'])->name('general');
                Route::post('/general-save', [SettingsController::class, 'generalSave']);

                Route::get('/openai', [SettingsController::class, 'openai'])->name('openai');
                Route::get('/openai/test', [SettingsController::class, 'openaiTest'])->name('openai.test');
                Route::post('/openai-save', [SettingsController::class, 'openaiSave']);

                Route::get('/stablediffusion', [SettingsController::class, 'stablediffusion'])->name('stablediffusion');
                Route::get('/stablediffusion/test', [SettingsController::class, 'stablediffusionTest'])->name('stablediffusion.test');
                Route::post('/stablediffusion-save', [SettingsController::class, 'stablediffusionSave']);

                Route::get('/tts', [SettingsController::class, 'tts'])->name('tts');
                Route::post('/tts-save', [SettingsController::class, 'ttsSave']);

                Route::get('/invoice', [SettingsController::class, 'invoice'])->name('invoice');
                Route::post('/invoice-save', [SettingsController::class, 'invoiceSave']);

                Route::get('/payment', [SettingsController::class, 'payment'])->name('payment');
                Route::post('/payment-save', [SettingsController::class, 'paymentSave']);

                Route::get('/affiliate', [SettingsController::class, 'affiliate'])->name('affiliate');
                Route::post('/affiliate-save', [SettingsController::class, 'affiliateSave']);

                Route::get('/smtp', [SettingsController::class, 'smtp'])->name('smtp');
                Route::post('/smtp-save', [SettingsController::class, 'smtpSave']);
                Route::post('/smtp-test', [SettingsController::class, 'smtpTest'])->name('smtp.test');

                Route::get('/gdpr', [SettingsController::class, 'gdpr'])->name('gdpr');
                Route::post('/gdpr-save', [SettingsController::class, 'gdprSave']);

                Route::get('/privacy', [SettingsController::class, 'privacy'])->name('privacy');
                Route::post('/privacy-save', [SettingsController::class, 'privacySave']);

                Route::get('/storage', [SettingsController::class, 'storage'])->name('storage');
                Route::post('/storage-save', [SettingsController::class, 'storagesave']);
            });

            //Affiliates
            Route::prefix('affiliates')->name('affiliates.')->group(function () {
                Route::get('/', [AdminController::class, 'affiliatesList'])->name('index');
                Route::get('/sent/{id}', [AdminController::class, 'affiliatesListSent'])->name('sent');
            });

            //Frontend
            Route::prefix('frontend')->name('frontend.')->group(function () {
                Route::get('/', [AdminController::class, 'frontendSettings'])->name('settings');
                Route::post('/settings-save', [AdminController::class, 'frontendSettingsSave']);

                Route::get('/section-settings', [AdminController::class, 'frontendSectionSettings'])->name('sectionsettings');
                Route::post('/section-settings-save', [AdminController::class, 'frontendSectionSettingsSave']);

                Route::get('/menu', [AdminController::class, 'menuSettings'])->name('menusettings');
                Route::post('/menu-save', [AdminController::class, 'menuSettingsSave']);

                //Frequently Asked Questions (F.A.Q) Section faq
                Route::prefix('faq')->name('faq.')->group(function () {
                    Route::get('/', [AdminController::class, 'frontendFaq'])->name('index');
                    Route::get('/create-or-update/{id?}', [AdminController::class, 'frontendFaqcreateOrUpdate'])->name('createOrUpdate');
                    Route::get('/action/delete/{id}', [AdminController::class, 'frontendFaqDelete'])->name('delete');
                    Route::post('/action/save', [AdminController::class, 'frontendFaqcreateOrUpdateSave']);
                });

                //Tools Section
                Route::prefix('tools')->name('tools.')->group(function () {
                    Route::get('/', [AdminController::class, 'frontendTools'])->name('index');
                    Route::get('/create-or-update/{id?}', [AdminController::class, 'frontendToolscreateOrUpdate'])->name('createOrUpdate');
                    Route::get('/action/delete/{id}', [AdminController::class, 'frontendToolsDelete'])->name('delete');
                    Route::post('/action/save', [AdminController::class, 'frontendToolscreateOrUpdateSave']);
                });

                //Future of ai section Features
                Route::prefix('future')->name('future.')->group(function () {
                    Route::get('/', [AdminController::class, 'frontendFuture'])->name('index');
                    Route::get('/create-or-update/{id?}', [AdminController::class, 'frontendFutureCreateOrUpdate'])->name('createOrUpdate');
                    Route::get('/action/delete/{id}', [AdminController::class, 'frontendFutureDelete'])->name('delete');
                    Route::post('/action/save', [AdminController::class, 'frontendFutureCreateOrUpdateSave']);
                });

                //who is this script for?
                Route::prefix('whois')->name('whois.')->group(function () {
                    Route::get('/', [AdminController::class, 'frontendWhois'])->name('index');
                    Route::get('/create-or-update/{id?}', [AdminController::class, 'frontendWhoisCreateOrUpdate'])->name('createOrUpdate');
                    Route::get('/action/delete/{id}', [AdminController::class, 'frontendWhoisDelete'])->name('delete');
                    Route::post('/action/save', [AdminController::class, 'frontendWhoisCreateOrUpdateSave']);
                });


                //Generator List
                Route::prefix('generatorlist')->name('generatorlist.')->group(function () {
                    Route::get('/', [AdminController::class, 'frontendGeneratorlist'])->name('index');
                    Route::get('/create-or-update/{id?}', [AdminController::class, 'frontendGeneratorlistCreateOrUpdate'])->name('createOrUpdate');
                    Route::get('/action/delete/{id}', [AdminController::class, 'frontendGeneratorlistDelete'])->name('delete');
                    Route::post('/action/save', [AdminController::class, 'frontendGeneratorlistCreateOrUpdateSave']);
                });
            });

            Route::resource('advertis', AdvertisController::class)->parameter('advertis', 'advertis');

            //Update
            Route::prefix('update')->name('update.')->group(function () {
                Route::get('/', function () {
                    return view('panel.admin.update.index');
                })->name('index');
            });

            //Healt Page
            Route::prefix('health')->name('health.')->group(function () {
                Route::get('/', function () {
                    $resultStore = App::make(ResultStore::class);
                    $checkResults = $resultStore->latestResults();

                    // call new status when visit the page
                    Artisan::call(RunHealthChecksCommand::class);

                    return view('panel.admin.health.index', [
                        'lastRanAt' => new Carbon($checkResults?->finishedAt),
                        'checkResults' => $checkResults,
                    ]);
                })->name('index');
            });
        });

        //Support Area
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/my-requests', [SupportController::class, 'list'])->name('list');
            Route::get('/new-support-request', [SupportController::class, 'newTicket'])->name('new');
            Route::post('/new-support-request/send', [SupportController::class, 'newTicketSend']);

            Route::get('/requests/{ticket_id}', [SupportController::class, 'viewTicket'])->name('view');
            Route::post('/requests-action/send-message', [SupportController::class, 'viewTicketSendMessage']);
        });

        //Pages
        Route::prefix('page')->name('page.')->group(function () {
            Route::get('/', [PageController::class, 'pageList'])->name('list');
            Route::get('/add-or-update/{id?}', [PageController::class, 'pageAddOrUpdate'])->name('addOrUpdate');
            Route::get('/delete/{id?}', [PageController::class, 'pageDelete'])->name('delete');
            Route::post('/save', [PageController::class, 'pageAddOrUpdateSave']);
        });

        //Email Templates
        Route::prefix('email-templates')->name('email-templates.')->group(function () {
            Route::get('/', [EmailTemplatesController::class, 'templateList'])->name('list');
            Route::get('/add-or-update/{id?}', [EmailTemplatesController::class, 'templateAddOrUpdate'])->name('addOrUpdate');
            //Route::get('/delete/{id?}', [EmailTemplatesController::class, 'templateDelete'])->name('delete');
            Route::post('/save', [EmailTemplatesController::class, 'templateAddOrUpdateSave']);
        });
        //Blog
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', [BlogController::class, 'blogList'])->name('list');
            Route::get('/add-or-update/{id?}', [BlogController::class, 'blogAddOrUpdate'])->name('addOrUpdate');
            Route::get('/delete/{id?}', [BlogController::class, 'blogDelete'])->name('delete');
            Route::post('/save', [BlogController::class, 'blogAddOrUpdateSave']);
        });

        //Search
        Route::post('/api/search', [SearchController::class, 'search']);
    });

    // Override amamarul routes
    Route::group(['prefix' => config('amamarul-location.prefix'), 'middleware' => config('amamarul-location.middlewares'), 'as' => 'amamarul.translations.'], function () {
        Route::get('/', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@index')->name('home');
        Route::get('lang/{lang}', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@lang')->name('lang');
        Route::get('lang/generateJson/{lang}', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@generateJson')->name('lang.generateJson');
        Route::get('newLang', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@newLang')->name('lang.newLang');
        Route::get('newString', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@newString')->name('lang.newString');
        Route::get('search', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@search')->name('lang.search');
        Route::get('string/{code}', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@string')->name('lang.string');
        Route::get('publish-all', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@publishAll')->name('lang.publishAll');
        //Reinstall
        Route::get('regenerate', function () {
            rename(
                storage_path('amamarul-locations/locations.sqlite'),
                storage_path('amamarul-locations/' . 'backup_' . date('Y_m_d_hms') . '_locations.sqlite')
            );
            Artisan::call('amamarul:location:install');
            return redirect()->route('amamarul.translations.home')->with(config('amamarul-location.message_flash_variable'), __('Language files regenerated!'));
        })->name('lang.reinstall');
        //setLocale
        Route::get('setLocale', function (\Illuminate\Http\Request $request) {
            $settings_two = \App\Models\SettingTwo::first();
            $settings_two->languages_default = $request->setLocale;
            $settings_two->save();
            LaravelLocalization::setLocale($request->setLocale);
            return redirect()->route('amamarul.translations.home', [$request->setLocale])->with(config('amamarul-location.message_flash_variable'), $request->setLocale);
        })->name('lang.setLocale');
    });

    Route::post('translations/lang/update/{id}', '\Amamarul\LaravelJsonLocationsManager\Controllers\HomeController@update')->name('amamarul.translations.lang.update');
    Route::post('translations/lang/update-all', function (\Illuminate\Http\Request $request) {

        $json = json_decode($request->data, true);
        $column_name = $request->lang;

        if ( $column_name == 'edit' ){
            foreach ($json as $code => $column_value) {
                ++$code;

                // if (empty($column_value)) {
                //     $column_value = \Amamarul\LaravelJsonLocationsManager\Models\Strings::select('en')
                //         ->where('code', '=', $code)
                //         ->value('en');
                // }

                $test = \Amamarul\LaravelJsonLocationsManager\Models\Strings::where('code', '=', $code)
                    ->update([$column_name => $column_value]);
            }
        } else {
            foreach ($json as $code => $column_value) {
                ++$code;
                $test = \Amamarul\LaravelJsonLocationsManager\Models\Strings::select()
                    ->where('code', '=', $code)
                    ->update([$column_name => $column_value]);
            }
        }

        $lang = $column_name;
        $list = \Amamarul\LaravelJsonLocationsManager\Models\Strings::pluck($lang, 'en');
        $new_json = json_encode_prettify($list);

        $filesystem = new \Illuminate\Filesystem\Filesystem;
        $filesystem->put(base_path('lang/' . $lang . '.json'), $new_json);
        if ( $column_name == 'edit' ){
            $lang = $column_name == 'edit' ? 'en' : $column_name;
            $filesystem->put(base_path('lang/' . $lang . '.json'), $new_json);
        }
        return response()->json(['code' => 200], 200);
    })->name('amamarul.translations.lang.update-all');

    Route::post('translations/lang-save', function (\Illuminate\Http\Request $request) {

        $settings_two = \App\Models\SettingTwo::first();
        $codes = explode(',', $settings_two->languages);

        if ($request->state) {
            if (!in_array($request->lang, $codes)) {
                $codes[] = $request->lang;
            }
        } else {
            if (in_array($request->lang, $codes)) {
                unset($codes[array_search($request->lang, $codes)]);
            }
        }
        $settings_two->languages = implode(',', $codes);
        $settings_two->save();
        return response()->json(['code' => 200], 200);
    })->name('amamarul.translations.lang.lang-save');
});


