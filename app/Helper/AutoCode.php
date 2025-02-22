<?php

namespace App\Helper;

use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderPayment;

class AutoCode{

    public static function randomString(){
        $string = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $result = '';
        for ($i=0; $i<8; $i++){
            $result .= $string[rand(0, strlen($string)-1)];
        }

        return $result;
    }

    public static function orderNumber($tenant): string{
        $kode = self::randomString();

        $check = Order::query()
            ->where('tenant_id', $tenant)
            ->where('number', '=', $kode)
            ->exists();

        if ($check){
            $kode = self::randomString();
        }

        return $kode;

//        $like = 'ORD'.date('Ym');
//
//        $data = Order::query()
//            ->where('tenant_id', $tenant)
//            ->where('number', 'like', $like.'%')
//            ->max('number');
//
//        $i = 0;
//        if ($data){
//            $i = (int) substr($data, -5);
//        }
//        $i++;
//
//        return$like.substr('00000'.$i, -5);
    }

    public static function paymentNumber($tenant): string{
        $kode = self::randomString();

        $check = OrderPayment::query()
            ->where('tenant_id', $tenant)
            ->where('number', '=', $kode)
            ->exists();

        if ($check){
            $kode = self::randomString();
        }

        return $kode;
//        $like = 'PAY'.date('Ym');
//
//        $data = OrderPayment::query()
//            ->where('tenant_id', $tenant)
//            ->where('number', 'like', $like.'%')
//            ->max('number');
//
//        $i = 0;
//        if ($data){
//            $i = (int) substr($data, -5);
//        }
//        $i++;
//
//        return$like.substr('00000'.$i, -5);
    }

    public static function expenseNumber($tenant): string{
        $kode = self::randomString();

        $check = Expense::query()
            ->where('tenant_id', $tenant)
            ->where('number', '=', $kode)
            ->exists();

        if ($check){
            $kode = self::randomString();
        }

        return $kode;
//        $like = 'EXP'.date('Ym');
//
//        $data = Expense::query()
//            ->where('tenant_id', $tenant)
//            ->where('number', 'like', $like.'%')
//            ->max('number');
//
//        $i = 0;
//        if ($data){
//            $i = (int) substr($data, -5);
//        }
//        $i++;
//
//        return$like.substr('00000'.$i, -5);
    }
}
