<?php

namespace Wikidot\Actions;
use Illuminate\Support\Facades\Hash;
use Ozone\Framework\Database\Criteria;
use Ozone\Framework\OzoneEmail;
use Ozone\Framework\SmartyAction;

use Wikidot\Utils\GlobalProperties;
use Wikidot\Utils\ProcessException;
use Wikijump\Models\User;

class PasswordRecoveryAction extends SmartyAction
{

    public function perform($r)
    {
    }

    public function step1Event($runData)
    {
        $pl = $runData->getParameterList();

        $email = $pl->getParameterValue("email", "AMODULE");
        if ($email == null || $email == '') {
            throw new ProcessException(_("Email must be provided."), "no_email");
        }

        if ($email == null || $email == '') {
            throw new ProcessException(_("Email must be provided."), "no_email");
        }

        if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/", $email) ==0) {
            throw new ProcessException(_("Valid email must be provided."), "no_email");
        }

        // check for users with the email
        $user = User::whereRaw('lower(email)', strtolower($email))->first();

        if ($user == null) {
            throw new ProcessException(_("This email cannot be found in our database."), "no_email");
        }

        // generate code
        srand((double)microtime()*1000000);
        $string = md5(rand(0, 9999));
        $evcode = substr($string, 2, 6);

        //send a confirmation email to the user.
        $oe = new OzoneEmail();
        $oe->addAddress($email);
        $oe->setSubject(sprintf(_("%s - password recovery"), GlobalProperties::$SERVICE_NAME));
        $oe->contextAdd("user", $user);
        $oe->contextAdd("email", $email);
        $oe->contextAdd('revcode', $evcode);

        $oe->setBodyTemplate('PasswordRecoveryEmail');

        if (!$oe->Send()) {
            throw new ProcessException(_("The email cannot be sent to this address."), "no_email");
        }

        $runData->sessionAdd("revcode", $evcode);
        $runData->sessionAdd("prUserId", $user->getUserId());
        $runData->contextAdd("email", $email);
    }

    public function step2Event($runData)
    {
        $pl = $runData->getParameterList();

        $evercode = $pl->getParameterValue("evercode");

        if ($evercode != $runData->sessionGet("revcode")) {
            throw new ProcessException(_("The verification codes do not match."), "form_error");
        }

        $password = $pl->getParameterValue("password");
        $password2 = $pl->getParameterValue("password2");

        // check password
        if (strlen8($password)<8) {
            throw new ProcessException(_("Password reset failed: Minimum password length is 8 characters."), "form_error");
        } elseif (strlen8($password)>256) {
                throw new ProcessException(_("Password reset failed: Maximum password length is 256 characters to avoid denial of service."), "form_error");
        } elseif ($password2 != $password) {
                throw new ProcessException(_("Passwords are not identical."), "form_error");
        }

        // ok. seems fine.

        $userId = $runData->sessionGet("prUserId");
        $user = User::find($userId);
        if ($user == null) {
            throw ProcessException("No such user.", "no_user");
        }

        $user->password = Hash::make($password);
        $user->save();
    }

    public function cancelEvent($runData)
    {
        // reset session etc.
        $runData->resetSession();
    }
}
