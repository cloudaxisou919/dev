<!-- Libs JS -->
<script src="/assets/libs/apexcharts/dist/apexcharts.min.js" defer></script>
<script src="/assets/libs/jsvectormap/dist/js/jsvectormap.min.js" defer></script>
<script src="/assets/libs/jsvectormap/dist/maps/world.js" defer></script>
<script src="/assets/libs/jsvectormap/dist/maps/world-merc.js" defer></script>
<!-- Tabler Core -->
<script src="/assets/js/tabler.min.js" defer></script>
<script src="/assets/js/opai.min.js" defer></script>

<!-- AJAX CALLS -->
<script src="/assets/openai/js/jquery.js"></script>
<script src="/assets/openai/js/main.js"></script>
<script src="/assets/openai/js/toastr.min.js"></script>
<script src="/assets/libs/tom-select/dist/js/tom-select.base.min.js?1674944402" defer></script>

<script>
    var magicai_localize = {
        signup: @json(__('Sign Up')),
        please_wait: @json(__('Please Wait...')),
        sign_in: @json(__('Sign In')),
        login_redirect: @json(__('Login Successful, Redirecting...')),
        register_redirect: @json(__('Registration is complete. Redirecting...')),
        password_reset_link: @json(__('Password reset link sent succesfully. Please also check your spam folder.')),
        password_reset_done: @json(__('Password succesfully changed.')),
        password_reset: @json(__('Reset Password')),
        missing_email: @json(__('Please enter your email address.')),
        missing_password: @json(__('Please enter your password.')),
        content_copied_to_clipboard: @json(__('Content copied to clipboard.')),
    }
</script>


<!-- PAGES JS-->
@guest()
<script src="/assets/js/panel/login_register.js"></script>
@endguest
<script src="/assets/js/panel/search.js"></script>

<script src="/assets/libs/list.js/dist/list.js" defer></script>


