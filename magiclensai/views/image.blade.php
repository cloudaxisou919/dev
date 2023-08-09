<!-- Start image generator -->
    <div class="row row-deck row-cards">
        <div class="col-12">
            <div class="card bg-[#F3E2FD] !shadow-sm dark:bg-[#14171C] dark:shadow-black">
                <div class="card-body md:p-10">
                    <div class="mb-3">
                        <div class="form-selectgroup flex flex-row">
                          <label class="form-selectgroup-item-image-gen" image-generator="dall-e">
                            <input type="radio" name="icons" value="dall-e" class="form-selectgroup-input" onclick="window.location.href = '{{route('dashboard.user.openai.generator', 'ai_image_generator')}}'"/>
                            <h3 class="form-selectgroup-label border-none dark:!text-white">DALL-E</h3>
                          </label>
                          <label class="form-selectgroup-item-image-gen" image-generator="stablediffusion">
                            <input type="radio" name="icons" value="stablediffusion" class="form-selectgroup-input" />
                            <h3 class="form-selectgroup-label border-none dark:!text-white">Stable Diffusion</h3>
                          </label>
                          <label class="form-selectgroup-item-image-gen" image-generator="magiclensai">
                            <input type="radio" name="icons" value="magiclensai" class="form-selectgroup-input"  checked/>
                            <h3 class="form-selectgroup-label border-none dark:!text-white">MagicLensAI</h3>
                          </label>
                        </div>
                    </div>
                    <div class="row">
                        <label for="description" class="h2 mb-3">{{__('Explain your idea')}}. <a onclick="return fillAnExample();" class="text-success" href=""></a> </label>
                        <form id="openai_generator_form" onsubmit="return sendOpenaiGeneratorForm();">
                            <div class="relative mb-3">
                                @php
                                    $placeholders = [
                                        'Cityscape at sunset in retro vector illustration ',
                                        'Painting of a flower vase on a kitchen table with a window in the backdrop.',
                                        'Memphis style painting of a flower vase on a kitchen table with a window in the backdrop.',
                                        'Illustration of a cat sitting on a couch in a living room with a coffee mug in its hand.',
                                        'Delicious pizza with all the toppings.'];
                                @endphp
                                        <input class="image-input-for-fillanexample form-control bg-[#fff] rounded-full h-[53px] text-[#000] !shadow-sm placeholder:text-black placeholder:text-opacity-50 focus:bg-white focus:border-white dark:!border-none dark:!bg-[--lqd-header-search-bg] dark:focus:!bg-[--lqd-header-search-bg] dark:placeholder:text-[#a5a9b1]" type="text" id="description" name="description" placeholder="{{$placeholders[array_rand($placeholders)]}}">
                                <button id="openai_generator_button" class="btn btn-primary generate-button h-[36px] absolute top-1/2 end-[1rem] -translate-y-1/2 hover:-translate-y-1/2 hover:scale-110 max-lg:relative max-lg:top-auto max-lg:right-auto max-lg:translate-y-0 max-lg:w-full max-lg:mt-2" type="submit">
                                    {{__('Generate')}}
                                    <svg class="!ms-2 rtl:-scale-x-100 translate-x-0 translate-y-0" width="14" height="13" viewBox="0 0 14 13" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.25 13L6.09219 11.8625L10.6422 7.3125H0.75V5.6875H10.6422L6.09219 1.1375L7.25 0L13.75 6.5L7.25 13Z"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="generator_sidebar_table">
        @include('generator_image_table')
        </div>
    </div>
<!-- End image generator -->
