<?php

namespace App\Observers;

use App\Enum\OrderStatus;
use App\Helper\Constant;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class OrderItemObserver{
    public function created(OrderItem $data): void{
        $order = Order::query()->find($data->order_id);

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d', strtotime($order->tgl)).' '.date('H:i:s', strtotime($order->jam)));
        $endDateTime = $startDateTime;
        if ($data->period_in == Constant::PERIOD_MINUTE){
            $endDateTime = $startDateTime->addMinutes((float) $data->duration);
        }if ($data->period_in == Constant::PERIOD_HOUR){
            $endDateTime = $startDateTime->addHours((float) $data->duration);
        }if ($data->period_in == Constant::PERIOD_DAY){
            $endDateTime = $startDateTime->addDays((float) $data->duration);
        }

        $upd = OrderItem::query()->find($data->id);
        $upd->start_date    = date('Y-m-d', strtotime($order->tgl));
        $upd->start_time    = date('H:i:s', strtotime($order->jam));
        $upd->end_date      = $endDateTime->format('Y-m-d');
        $upd->end_time      = $endDateTime->format('H:i:s');
        $upd->save();
    }

    public function updated(OrderItem $data): void{
        $oldCheck = $data->getOriginal('sudah_kembali');

        if ($oldCheck != $data->sudah_kembali){

            $selectAll   = OrderItem::query()
                ->where('order_id', $data->order_id)
                ->count();
            $selectSudah = OrderItem::query()
                ->where('order_id', $data->order_id)
                ->where('sudah_kembali', true)
                ->count();

            if ($selectSudah == $selectAll){
                $order = Order::query()->find($data->order_id);
                $order->status = OrderStatus::Done;
                $order->save();
            }else if ($selectSudah > 0 && $selectSudah < $selectAll){
                $order = Order::query()->find($data->order_id);
                $order->status = OrderStatus::Partial;
                $order->save();
            }else if($selectSudah == 0){
                $order = Order::query()->find($data->order_id);
                $order->status = OrderStatus::Processing;
                $order->save();
            }

            $upd2 = OrderItem::query()->find($data->id);
            $upd2->ket = "sudah: {$selectSudah} Semua: {$selectAll}";
            $upd2->save();

            if ($data->sudah_kembali && !$data->tgl_kembali){
                $upd = OrderItem::query()->find($data->id);
                $upd->tgl_kembali = now();
                $upd->save();
            }
        }
    }

    public function deleted(OrderItem $data): void{
//
    }

    public function restored(OrderItem $data){
//
    }

    public function forceDeleted(OrderItem $order){
//
    }
}
