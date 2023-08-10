<div class="col-12">
    <div class="row">
    <div class="w-full">
        @if(isset($resultHTML) && !empty($resultHTML))
             <h2 class="mb-3">{{__('Result')}}</h2>
             @endif
        <div class="image-results row" id="append-images">
            @php echo $resultHTML @endphp  
        </div> 
    </div>
</div>
</div>


