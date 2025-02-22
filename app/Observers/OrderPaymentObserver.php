<?php

namespace App\Observers;

use App\Enum\PaymentStatus;
use App\Models\Order;
use App\Models\OrderPayment;

class OrderPaymentObserver{

    public function created(OrderPayment $data): void{
        $order = Order::query()->find($data->order_id);

        $order->paid    = $order->paid + $data->nominal;
        $order->kurang  = $order->subtotal - $order->paid;

        if ($order->paid > 0){
            $order->payment_status = PaymentStatus::Partial;
        }

        if ($order->kurang <= 0){
            $order->payment_status = PaymentStatus::Paid;
        }

        $order->save();
    }

    public function updated(OrderPayment $data): void{
        $order = Order::query()->find($data->order_id);

        $originalNominal = $data->getOriginal('nominal');
        $newNominal = $data->nominal;

        if ($newNominal != $originalNominal){
            $order->paid    = $order->paid - $originalNominal + $newNominal;
            $order->kurang  = $order->subtotal - $order->paid;

            if ($order->paid > 0){
                $order->payment_status = PaymentStatus::Partial;
            }

            if ($order->kurang <= 0){
                $order->payment_status = PaymentStatus::Paid;
            }

            $order->save();
        }
    }

    public function deleted(OrderPayment $data): void{
        $order = Order::query()->find($data->order_id);

        $order->paid    = $order->paid - $data->nominal;
        $order->kurang  = $order->subtotal - $order->paid;

        if ($order->paid > 0){
            $order->payment_status = PaymentStatus::Partial;
        }

        if ($order->kurang <= 0){
            $order->payment_status = PaymentStatus::Paid;
        }

        if ($order->paid == 0){
            $order->payment_status = PaymentStatus::Unpaid;
        }

        $order->save();
    }

    public function restored(OrderPayment $data){
//
    }

    public function forceDeleted(OrderPayment $order){
//
    }
}
