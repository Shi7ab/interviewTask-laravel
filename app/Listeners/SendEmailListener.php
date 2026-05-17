<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailListener 
{
    public function handle(TaskCreated $event)
    {
        $user = $event->task->user;

        Mail::to($user->email)->send(
            new SendEmail($event->task)
        );
      /*  Log::info('TaskCreated Event Fired', [
            'task_id' => $event->task->id,
            'title' => $event->task->title,
            'user_id' => $event->task->user_id,
        ]);*/

    }
}

