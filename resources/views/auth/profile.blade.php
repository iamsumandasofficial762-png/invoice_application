<x-layouts.app title="Profile">
    <section class="page-header">
        <div>
            <p class="eyebrow">Account Settings</p>
            <h1>Profile</h1>
        </div>
    </section>

    <form method="POST" action="{{ route('profile.update') }}" class="form-card profile-form profile-card">
        @csrf
        @method('PUT')

        <div class="profile-card-header">
            <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
                <h2>Account Information</h2>
                <p>Update your login name, email, and password.</p>
            </div>
        </div>

        <div class="profile-form-grid">
            <label class="profile-field">
                <span>Name</span>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </label>

            <label class="profile-field">
                <span>Email / username</span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </label>

            <label class="profile-field">
                <span>Current password</span>
                <div class="profile-password-input">
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password">
                    <button class="profile-password-toggle" type="button" data-password-toggle data-password-target="current_password" aria-label="Show current password">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </label>

            <label class="profile-field">
                <span>New password</span>
                <div class="profile-password-input">
                    <input id="new_password" type="password" name="new_password" autocomplete="new-password">
                    <button class="profile-password-toggle" type="button" data-password-toggle data-password-target="new_password" aria-label="Show new password">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                @error('new_password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </label>

            <label class="profile-field">
                <span>Confirm new password</span>
                <div class="profile-password-input">
                    <input id="new_password_confirmation" type="password" name="new_password_confirmation" autocomplete="new-password">
                    <button class="profile-password-toggle" type="button" data-password-toggle data-password-target="new_password_confirmation" aria-label="Show confirm password">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </label>
        </div>

        <div class="profile-form-actions">
            <button class="btn btn-primary" type="submit">Update Profile</button>
        </div>
    </form>

    @push('scripts')
        <script src="{{ asset('assets/js/profile-password-toggle.js') }}"></script>
    @endpush
</x-layouts.app>
