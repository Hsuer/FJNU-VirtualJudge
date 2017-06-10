<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\SendReminderEmail;
use Mail;
use Storage;
use App\User;

class MailController extends Controller
{
    public function sendReminderEmail(Request $request,$id){
        $user = User::findOrFail($id);
        echo "发送成功";
        $this->dispatch((new SendReminderEmail($user))->delay(60));
    }
}