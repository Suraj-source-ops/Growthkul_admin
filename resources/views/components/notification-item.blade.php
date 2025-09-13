<tr style="cursor: pointer;">
    <td>
        <div class="notification-content-box">
            <div class="noti-bell-img">
                <img class="" src="{{ asset('/assets/images/bell-icon.png') }}" alt="back-icon" />
            </div>
            <div class="right-notification-text">
                <h4>{{ ucfirst($notification->action) ?? '' }}
                    @if ($notification->status == 0)
                    <span style="float: right; color:#5646c4; font-size:12px"><i class="fa fa-circle"></i></span>
                    @endif
                </h4>
                <p>{{ $notification->note ?? '' }}</p>
                <p>{{ $notification->created_at->format('Y-m-d h:i:s A') }}</p>
            </div>
        </div>
    </td>
</tr>