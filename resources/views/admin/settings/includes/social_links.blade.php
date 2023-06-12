<div class="tile">
    <form action="{{ route('admin.settings.update') }}" method="POST" role="form">
        @csrf
        <h3 class="tile-title">Social Links</h3>
        <hr>
        <div class="tile-body">
            <div class="form-group">
                <label class="control-label" for="social_facebook">Facebook Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter facebook profile link"
                    id="social_facebook"
                    name="social_facebook"
                    value="{{ config('settings.social_facebook') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="social_twitter">Twitter Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter twitter profile link"
                    id="social_twitter"
                    name="social_twitter"
                    value="{{ config('settings.social_twitter') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="social_instagram">Instagram Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter instagram profile link"
                    id="social_instagram"
                    name="social_instagram"
                    value="{{ config('settings.social_instagram') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="social_telegram">Telegram Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter telegram profile link"
                    id="social_telegram"
                    name="social_telegram"
                    value="{{ config('settings.social_telegram') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="social_viber">Viber Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter viber profile link"
                    id="social_viber"
                    name="social_viber"
                    value="{{ config('settings.social_viber') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="social_youtube">Youtube Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter youtube profile link"
                    id="social_youtube"
                    name="social_youtube"
                    value="{{ config('settings.social_youtube') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="social_whatsapp">WhatsApp Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter whatsapp profile link"
                    id="social_whatsapp"
                    name="social_whatsapp"
                    value="{{ config('settings.social_whatsapp') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="social_linkedin">LinkedIn Profile</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Enter linkedin profile link"
                    id="social_linkedin"
                    name="social_linkedin"
                    value="{{ config('settings.social_linkedin') }}"
                />
            </div>
        </div>
        <div class="tile-footer">
            <div class="row d-print-none mt-2">
                <div class="col-12 text-right">
                    <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update Settings</button>
                </div>
            </div>
        </div>
    </form>
</div>
