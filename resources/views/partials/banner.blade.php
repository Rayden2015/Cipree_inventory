@php
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

// Check if banner is enabled
$bannerEnabled = config('banner.enabled', true);
$bannerDismissible = config('banner.dismissible', false);
$disableDate = config('banner.disable_date', '2026-01-01');

// Format the date for display
try {
    $disableDateFormatted = Carbon::parse($disableDate)->format('F j, Y');
} catch (\Exception $e) {
    $disableDateFormatted = $disableDate; // Fallback to raw value if parsing fails
}

// Determine if banner should show
// If not dismissible: show on every page load after login (if enabled)
// If dismissible: show only on first page load after login (if user hasn't dismissed)
if (!$bannerDismissible) {
    // Non-dismissible: show on every page load if enabled and user is logged in
    $shouldShowBanner = $bannerEnabled && Auth::check();
} else {
    // Dismissible: show only if user hasn't dismissed and session flag is set
    $shouldShowBanner = $bannerEnabled && !Auth::user()->banner_dismissed_at && Session::has('show_banner_on_login');
    // Clear the flag after checking (only for dismissible banner)
    if ($shouldShowBanner) {
        Session::forget('show_banner_on_login');
    }
}
@endphp

<!-- Banner Modal -->
@if($bannerEnabled)
<div class="modal fade" id="cipreeBannerModal" tabindex="-1" role="dialog" aria-labelledby="cipreeBannerModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #20c997; color: white;">
                <h4 class="modal-title" id="cipreeBannerModalLabel" style="font-weight: bold;">Cipree Inventory</h4>
                @if($bannerDismissible)
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                @endif
            </div>
            <div class="modal-body" style="padding: 30px;">
                <h3 style="font-weight: bold; margin-bottom: 20px; color: #333;">It's been a great 2 years!</h3>
                <p style="font-size: 16px; line-height: 1.6; color: #555; margin-bottom: 25px;">
                    We've loved having you as a tester for Cipree Inventory. As we move out of our initial testing phase on <strong>{{ $disableDateFormatted }}</strong>, we invite you to transition to a full account.
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: #555; margin-bottom: 30px;">
                    Don't worry—all your data will stay exactly where it is. Simply pick a plan to keep your operations running smoothly.
                </p>
                <div class="text-center" style="margin-top: 30px;">
                    <button type="button" class="btn btn-lg" id="explorePlansBtn" style="margin-right: 15px; padding: 10px 30px; font-size: 16px; background-color: #20c997; border-color: #20c997; color: white;">
                        Explore Plans
                    </button>
                    @if($bannerDismissible)
                    <button type="button" class="btn btn-secondary btn-lg" id="maybeLaterBtn" style="padding: 10px 30px; font-size: 16px;">
                        Maybe Later
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Contact Administrator Modal -->
<div class="modal fade" id="contactAdminModal" tabindex="-1" role="dialog" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #20c997; color: white;">
                <h4 class="modal-title" id="contactAdminModalLabel" style="font-weight: bold;">Contact Administrator</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 30px; text-align: center;">
                <p style="font-size: 18px; line-height: 1.6; color: #333;">
                    Please contact your administrator to explore available plans.
                </p>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button type="button" class="btn" id="contactAdminOkBtn" style="padding: 10px 30px; background-color: #20c997; border-color: #20c997; color: white;">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    @if($bannerEnabled && $shouldShowBanner)
        // Show modal - always show if enabled and should show
        $('#cipreeBannerModal').modal({
            backdrop: 'static',
            keyboard: {{ $bannerDismissible ? 'true' : 'false' }},
            show: true
        });
        
        // Prevent closing if not dismissible
        @if(!$bannerDismissible)
        $('#cipreeBannerModal').on('hide.bs.modal', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
        @endif
    @endif

    @if($bannerDismissible)
    // Handle "Maybe Later" button - dismiss for this session only (only if dismissible)
    $('#maybeLaterBtn').on('click', function() {
        $('#cipreeBannerModal').modal('hide');
        
        // Set session flag to prevent showing again in this session
        $.ajax({
            url: '{{ route("banner.dismiss.session") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Banner dismissed for this session');
            }
        });
    });
    @endif

    // Handle "Explore Plans" button - show contact administrator modal
    $('#explorePlansBtn').on('click', function() {
        @if($bannerDismissible)
        $('#cipreeBannerModal').modal('hide');
        // Show contact administrator modal after a short delay
        setTimeout(function() {
            $('#contactAdminModal').modal('show');
        }, 300);
        @else
        // If not dismissible, just show the contact admin modal without hiding banner
        $('#contactAdminModal').modal('show');
        @endif
    });

    @if($bannerDismissible)
    // Handle "OK" button in contact admin modal - permanently dismiss banner (only if dismissible)
    $('#contactAdminOkBtn').on('click', function() {
        $('#contactAdminModal').modal('hide');
        
        // Permanently dismiss the banner
        $.ajax({
            url: '{{ route("banner.dismiss.permanent") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Banner permanently dismissed');
                $('#cipreeBannerModal').modal('hide');
            }
        });
    });
    @else
    // If not dismissible, just close the contact admin modal
    $('#contactAdminOkBtn').on('click', function() {
        $('#contactAdminModal').modal('hide');
    });
    @endif
});
</script>

<style>
#cipreeBannerModal .modal-content {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

#cipreeBannerModal .modal-header {
    border-radius: 10px 10px 0 0;
}

#cipreeBannerModal .modal-body {
    border-radius: 0 0 10px 10px;
}

#contactAdminModal .modal-content {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

#contactAdminModal .modal-header {
    border-radius: 10px 10px 0 0;
}
</style>

