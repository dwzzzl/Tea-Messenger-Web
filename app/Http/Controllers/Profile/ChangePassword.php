<?php

/**
 *  Generated by IceTea Framework 0.0.1
 *  Created at 2017-12-14 15:50:38
 *  Namespace App\Http\Controllers\Profile
 */

namespace App\Http\Controllers\Profile;

use App\User;
use App\Login;
use IceTea\Http\Controller;
use App\Http\Controllers\Auth\CSRFToken;
use App\Http\Controllers\Auth\JSONResponse;

class ChangePassword extends Controller
{
    use CSRFToken, JSONResponse;

    public function __construct()
    {
        header("Content-type:application/json");
        parent::__construct();
    }

    public function index()
    {
        //
    }

    public function run()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset(
            $input['old_password'],
            $input['new_password'],
            $input['new_cpassword'],
            $input['csrf'],
            $input['cost']
        )) {
            if (! $this->csrfValidation($input['csrf'])) {
                $this->err("Token mismatch", "?err=token_mismatch&w=".urlencode(rstr(64)));
            }
            $this->validate($input);
            $this->passwordMatch($input['old_password']);
            $this->update($input['new_password']);
        }
    }

    private function validate($input)
    {
        $input['new_password'] === $input['new_cpassword'] or $this->err("Confirm password does not match!");
        $len = strlen($input['new_password']);
        $len >= 6 or $this->err("Password too short, please provide password more than 6 characters!");
        (!preg_match("#[^[:print:]]#", $input['new_password'])) or $this->err("Password must not contains unprintable chars!");
    }

    private function passwordMatch($password)
    {
        $bcrypt = Login::getBcryptHash(Login::getUserId());
        if (! isset($bcrypt[0]) || ! password_verify($password, $bcrypt[0])) {
            $this->err("Wrong old password!");
        }
    }

    private function update($input)
    {
        if (User::changePassword(Login::getUserId(), $input)) {
            exit(
                $this->buildJson(
                    [
                        "status" => "ok",
                        "message"=> "Your password has been changed successfully!",
                        "redirect"=>"?ref=change_password&w=".urlencode(rstr(64))
                    ]
                )
            );
        } else {
            $this->err("Internal error");
        }
    }
}
