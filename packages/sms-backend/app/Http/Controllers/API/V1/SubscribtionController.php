<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\UserType;
use App\Models\SubscribtionUser;
use App\Models\SupervisorType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscribtionController extends BaseApiController
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'subscribtion_package_id' => 'required|exists:subscribtion_packages,id',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'expiration_date' => 'required|date_format:Y-m-d',
        ]);

        DB::beginTransaction();
        try {
            $subscribtionUser = SubscribtionUser::create([
                'subscribtion_package_id' => $request->subscribtion_package_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'expiration_date' => $request->expiration_date ?? date('Y-m-d', strtotime('+1 month')),
            ]);

            $user = User::create([
                'subscribtion_user_id' => $subscribtionUser->id,
                'name' => $subscribtionUser->name,
                'email' => $subscribtionUser->email,
                'password' => bcrypt(12345678),
                'type' => UserType::DEFAULT,
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => 2,
                'model_type' => 'user',
                'model_id' => $user->id,
                'subscribtion_user_id' => $subscribtionUser->id
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }

        return response()->json(['message' => 'User created successfully'], 201);
    }
}
