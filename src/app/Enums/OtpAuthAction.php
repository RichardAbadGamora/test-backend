<?php

namespace App\Enums;

class OtpAuthAction
{
    const EMAIL_PASS_LOGIN = 'email-pass-login';
    const EMAIL_PASS_REGISTRATION = 'email-pass-registration';

    const EMAIL_ONLY_LOGIN = 'email-only-login';
    const EMAIL_ONLY_REGISTRATION = 'email-only-registration';

    const PHONE_ONLY_LOGIN = 'phone-only-login';
    const PHONE_ONLY_REGISTRATION = 'phone-only-registration';

    const MAGIC_LINK_LOGIN = 'magic-link-login';
    const MAGIC_LINK_REGISTRATION = 'magic-link-registration';
}
