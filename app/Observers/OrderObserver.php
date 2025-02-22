<?php

namespace App\Observers;

use App\Enum\OrderStatus;
use App\Helper\Constant;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderPayment;

class OrderObserver{

    public function created(Order $order): void{
        $history = new OrderHistory();
        $history->order_id  = $order->id;
        $history->status    = OrderStatus::New;
        $history->save();
    }

    public function updated(Order $order): void{
        $originalStatus = $order->getOriginal('status');

        if ($originalStatus != $order->status){
            $history = new OrderHistory();
            $history->order_id  = $order->id;
            $history->status    = $order->status;
            $history->save();
        }

        if ($order->status === OrderStatus::Cancelled){
            OrderPayment::query()
                ->where('order_id', $order->id)
                ->delete();
        }
    }

    public function deleted(Order $order){
//
    }

    public function restored(Order $order){
//
    }

    public function forceDeleted(Order $order){
//
    }
}
