<div class="mb-4 bg-gray-100 dark:bg-gray-800 dark:text-gray-200 p-4 rounded-md">
    @foreach($this->getUser()->recoveryCodes() as $code)
        <p class="text-sm font-medium mb-2">{{$code}}</p>
    @endforeach
</div>
