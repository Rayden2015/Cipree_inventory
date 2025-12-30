<!-- Banner Modal -->
<div class="modal fade" id="cipreeBannerModal" tabindex="-1" role="dialog" aria-labelledby="cipreeBannerModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #20c997; color: white;">
                <h4 class="modal-title" id="cipreeBannerModalLabel" style="font-weight: bold;">Cipree Inventory</h4>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <h3 style="font-weight: bold; margin-bottom: 20px; color: #333;">It's been a great 2 years!</h3>
                <p style="font-size: 16px; line-height: 1.6; color: #555; margin-bottom: 25px;">
                    We've loved having you as a tester for Cipree Inventory. As we move out of our initial testing phase on <strong>December 31, 2025</strong>, we invite you to transition to a full account.
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: #555; margin-bottom: 30px;">
                    Don't worry—all your data will stay exactly where it is. Simply pick a plan to keep your operations running smoothly.
                </p>
                <div class="text-center" style="margin-top: 30px;">
                    <button type="button" class="btn btn-lg" id="explorePlansBtn" style="margin-right: 15px; padding: 10px 30px; font-size: 16px; background-color: #20c997; border-color: #20c997; color: white;">
                        Explore Plans
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg" id="maybeLaterBtn" style="padding: 10px 30px; font-size: 16px;">
                        Maybe Later
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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

@php
use Illuminate\Support\Facades\Session;
$shouldShowBanner = !Auth::user()->banner_dismissed_at && Session::has('show_banner_on_login');
// Clear the flag immediately so it won't show on next page load
if ($shouldShowBanner) {
    Session::forget('show_banner_on_login');
}
@endphp

<script>
$(document).ready(function() {
    // Show modal only once per login session if user hasn't dismissed it permanently
    @if($shouldShowBanner)
        $('#cipreeBannerModal').modal('show');
    @endif

    // Handle "Maybe Later" button - dismiss for this session only
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

    // Handle "Explore Plans" button - show contact administrator modal
    $('#explorePlansBtn').on('click', function() {
        $('#cipreeBannerModal').modal('hide');
        // Show contact administrator modal after a short delay
        setTimeout(function() {
            $('#contactAdminModal').modal('show');
        }, 300);
    });

    // Handle "OK" button in contact admin modal - permanently dismiss banner
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
            }
        });
    });
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

