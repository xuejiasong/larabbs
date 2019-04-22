<?php

namespace App\Http\Controllers\Api;

use App\Transformers\NotificationTranformer;
use Illuminate\Http\Request;


class NotificationsController extends Controller
{
    //
    public function index()
    {
        $notifications = $this->user->notifications()->paginate(20);

        return $this->response->paginator($notifications, new NotificationTranformer());
    }

    public function stats(){
        return $this->response->array([
           'unread_count' => $this->user()->notification_count,
        ]);
    }

    public function read()
    {
        $this->user()->markRead();

        return $this->response->noContent();
    }
}
