<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-3">Social Links</h5>
        <div class="mb-3"><label class="form-label"><i class="bi bi-facebook me-1"></i>Facebook URL</label>
            <input type="text" name="facebook_url" class="form-control" maxlength="255" value="{{ old('facebook_url', $settings['facebook_url']) }}"></div>
        <div class="mb-3"><label class="form-label"><i class="bi bi-instagram me-1"></i>Instagram URL</label>
            <input type="text" name="instagram_url" class="form-control" maxlength="255" value="{{ old('instagram_url', $settings['instagram_url']) }}"></div>
        <div class="mb-3"><label class="form-label"><i class="bi bi-twitter-x me-1"></i>Twitter / X URL</label>
            <input type="text" name="twitter_url" class="form-control" maxlength="255" value="{{ old('twitter_url', $settings['twitter_url']) }}"></div>
        <div class="mb-3"><label class="form-label"><i class="bi bi-youtube me-1"></i>YouTube URL</label>
            <input type="text" name="youtube_url" class="form-control" maxlength="255" value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}"></div>
    </div>
</div>
