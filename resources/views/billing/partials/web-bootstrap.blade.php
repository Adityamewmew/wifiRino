{{-- Base URL API: dari request nyata (subfolder XAMPP OK tanpa mengubah APP_URL) --}}
<script>
    window.SS_API_BASE = @json(rtrim(request()->getSchemeAndHttpHost().request()->getBasePath().'/api', '/'));
</script>
@if (session('staff_token'))
    <script>
        (function() {
            try {
                localStorage.setItem('ss_token', @json(session('staff_token')));
                var u = @json(session('staff_user'));
                localStorage.setItem('ss_user', JSON.stringify(u));
            } catch (e) {}
        })();
    </script>
@endif
@if (session('portal_customer'))
    <script>
        (function() {
            try {
                var c = @json(session('portal_customer'));
                localStorage.setItem('ss_customer', JSON.stringify(c));
            } catch (e) {}
        })();
    </script>
@endif
