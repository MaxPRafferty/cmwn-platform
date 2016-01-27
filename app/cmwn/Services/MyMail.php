<?php

namespace app\cmwn\Services;

use Illuminate\Support\Facades\Mail;

class MyMail
{
    public static function send($mailData)
    {
        // var_dump($mailData);

        Mail::send($mailData['template'], $mailData, function ($message) use ($mailData) {
            $message->from('notifications@ginasink.com', 'Gina\'s Ink');
            //$message->cc('jon@ginasink.com', 'Jon at Ginasink');
            $message->replyTo('notifications@ginasink.com');
            $message->subject($mailData['subject']);
            $message->priority($mailData['priority']);
            $message->to($mailData['to']);
        });
    }
}
