<?php

namespace App\Traits;

use App\Enums\ProductCategory;
use App\Exceptions\SubscribtionException;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\User;

trait SaveToSubscriber
{
    public static function bootSaveToSubscriber()
    {
        static::creating(function ($model) {
            self::subscribtionValidation($model);
            if (is_null($model->subscribtion_user_id)) $model->subscribtion_user_id = auth()->user()->subscribtion_user_id ?? null;
        });
    }

    protected static function subscribtionValidation($model)
    {
        $user = auth()->user();
        if (!$user) return;
        if ($user->is_super_admin) return;

        if ($model instanceof User) {
            $total = User::where('subscribtion_user_id', $user->subscribtion_user_id)->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_users ?? 0) {
                throw new SubscribtionException();
            }
        }

        if ($model instanceof Customer) {
            $total = Customer::where('subscribtion_user_id', $user->subscribtion_user_id)->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_customers ?? 0) {
                throw new SubscribtionException();
            }
        }

        if ($model instanceof ProductBrand) {
            $total = ProductBrand::where('subscribtion_user_id', $user->subscribtion_user_id)->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_brands ?? 0) {
                throw new SubscribtionException();
            }
        }

        if ($model instanceof ProductCategory) {
            $total = ProductCategory::where('subscribtion_user_id', $user->subscribtion_user_id)->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_categories ?? 0) {
                throw new SubscribtionException();
            }
        }

        if ($model instanceof Product) {
            $total = Product::where('subscribtion_user_id', $user->subscribtion_user_id)->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_products ?? 0) {
                throw new SubscribtionException();
            }
        }

        if ($model instanceof Order) {
            $total = Order::where('subscribtion_user_id', $user->subscribtion_user_id)->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_orders ?? 0) {
                throw new SubscribtionException();
            }
        }

        if ($model instanceof Activity) {
            $total = Activity::whereHas('channel', fn ($q) => $q->where('subscribtion_user_id', $user->subscribtion_user_id))->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_activities ?? 0) {
                throw new SubscribtionException();
            }
        }

        if ($model instanceof Lead) {
            $total = Lead::whereHas('channel', fn ($q) => $q->where('subscribtion_user_id', $user->subscribtion_user_id))->count();
            if ($total >= $user->subscribtionUser?->subscribtionPackage?->max_leads ?? 0) {
                throw new SubscribtionException();
            }
        }
    }
}
