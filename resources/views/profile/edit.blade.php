    <x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Account Settings
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage your account information, password, and account security.
            </p>
        </div>
    </x-slot>

    <div class="profile-settings-page">
        <div class="profile-settings-container">

            {{-- Account Information --}}
            <section class="profile-settings-card">
                <div class="profile-card-heading">
                    <div class="profile-icon">👤</div>
                    <div>
                        <h3>Account Information</h3>
                        <p>Update your name and email address.</p>
                    </div>
                </div>

                <div class="profile-form-content">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </section>

            {{-- Change Password --}}
            <section class="profile-settings-card">
                <div class="profile-card-heading">
                    <div class="profile-icon">🔒</div>
                    <div>
                        <h3>Change Password</h3>
                        <p>Keep your account secure with a strong password.</p>
                    </div>
                </div>

                <div class="profile-form-content">
                    @include('profile.partials.update-password-form')
                </div>
            </section>

            {{-- Delete Account --}}
            <section class="profile-settings-card profile-danger-card">
                <div class="profile-card-heading">
                    <div class="profile-icon">⚠️</div>
                    <div>
                        <h3>Delete Account</h3>
                        <p>
                            Permanently delete your account and associated
                            account data. This action may not be reversible.
                        </p>
                    </div>
                </div>

                <div class="profile-form-content">
                    @include('profile.partials.delete-user-form')
                </div>
            </section>

        </div>
    </div>

    <style>
        .profile-settings-page {
            min-height: calc(100vh - 80px);
            padding: 30px 20px;
            background: #f3f5f9;
        }

        .profile-settings-container {
            max-width: 1050px;
            margin: 0 auto;
            display: grid;
            gap: 24px;
        }

        .profile-settings-card {
            overflow: hidden;
            padding: 28px;
            border: 1px solid #e5eaf1;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 8px 30px rgba(15, 35, 65, 0.05);
        }

        .profile-card-heading {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 22px;
            margin-bottom: 24px;
            border-bottom: 1px solid #edf0f5;
        }

        .profile-icon {
            display: flex;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #eaf2ff;
            font-size: 22px;
        }

        .profile-card-heading h3 {
            margin: 0;
            color: #14233b;
            font-size: 19px;
            font-weight: 700;
        }

        .profile-card-heading p {
            margin: 5px 0 0;
            color: #78869a;
            font-size: 13px;
            line-height: 1.6;
        }

        .profile-form-content {
            max-width: 760px;
        }

        .profile-danger-card {
            border-color: #f4d9dc;
        }

        .profile-danger-card .profile-icon {
            background: #fff0f1;
        }

        .profile-danger-card .profile-card-heading h3 {
            color: #a61b2b;
        }

        @media (max-width: 640px) {
            .profile-settings-page {
                padding: 18px 12px;
            }

            .profile-settings-card {
                padding: 19px;
            }

            .profile-card-heading {
                align-items: flex-start;
            }
        }
    </style>
</x-app-layout>
