<?php

namespace App\Services;

use App\Enums\UserType;
use App\Exports\ModelExport;
use App\Models\Channel;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Enums\NotificationType;
use App\Classes\ExpoMessage;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Voucher;

class CoreService
{
    use AuthorizesRequests;

    /**
     * Get enum contracts to generate
     * @return array
     */
    public static function getEnumContracts(): array
    {
        return collect(Storage::disk('enums')->allFiles())
            // remove php extension
            ->map(fn (string $filename) => (string)Str::of($filename)->before('.'))

            // remove undesired class
            ->filter(function (string $classname) {
                return is_a(sprintf("App\\Enums\\%s", $classname), Enum::class, true) &&
                    ($classname != "BaseEnum");
            })

            // map to desired output format
            ->map(function (string $classname) {
                $class = sprintf("App\\Enums\\%s", $classname);
                return $class::getContract();
            })
            ->values()
            ->toArray();
    }

    /**
     * Create an instance of stock for all product.
     * @param Channel $channel
     */
    public static function createStocksForChannel(Channel $channel)
    {

        Product::query()
            ->select(['id', 'company_id'])
            ->where('company_id', $channel->company_id)
            ->chunk(500, function ($products) use ($channel) {
                $ids = $products->pluck('id');

                // check existing stock to prevent duplicate
                $existingStockIds = Stock::query()
                    ->whereIn('id', $ids)
                    ->pluck('id');

                $stockData = $ids->diff($existingStockIds)
                    ->map(function (int $id) use ($channel) {
                        return [
                            'channel_id'      => $channel->id,
                            'product_id' => $id,
                            'company_id'      => $channel->company_id,
                        ];
                    });

                Stock::insert($stockData->all());
            });
    }

    /**
     * Create an instance of stock for all product.
     * @param Location $location
     */
    public static function createStocksForLocation(Location $location)
    {

        Product::query()
            ->select(['id', 'company_id'])
            ->chunk(500, function ($products) use ($location) {
                $ids = $products->pluck('id');

                // check existing stock to prevent duplicate
                $existingStockIds = Stock::query()
                    ->whereIn('id', $ids)
                    ->pluck('id');

                $stockData = $ids->diff($existingStockIds)
                    ->map(function (int $id) use ($location) {
                        return [
                            // 'channel_id'      => $channel->id,
                            'location_id' => $location->id,
                            'product_id' => $id,
                            // 'company_id'      => $channel->company_id,
                        ];
                    });

                Stock::insert($stockData->all());
            });
    }

    /**
     * Create an instance of stock of a product for all channel
     * in the company.
     * @param Product $product
     */
    public static function createStocksForProduct(Product $product)
    {
        // $ids = Channel::query()
        //     ->where('company_id', $product->company_id)
        //     ->pluck('id');

        $ids = Location::pluck('id');

        $existingId = Stock::query()
            ->where('product_id', $product->id)
            ->pluck('id');

        $stockData = $ids->diff($existingId)
            ->map(function (int $id) use ($product) {
                return [
                    // 'channel_id' => $id,
                    'location_id' => $id,
                    'product_id' => $product->id,
                    // 'company_id' => $product->company_id,
                ];
            });

        Stock::insert($stockData->all());
    }

    /**
     * Calculating product calculated_hpp
     * @param Product $product
     */
    public static function calculatingHPP(Product $product)
    {
        $productBrand = $product->brand;

        $productBrandHppCalculation = $productBrand->hpp_calculation;
        $currency = $productBrand->currency;
        if ($currency && $productBrandHppCalculation != 0) {
            $calculatedHpp = ($product->purchase_price * $currency->value) + ($product->purchase_price * ($productBrandHppCalculation / 100) * $currency->value);
            $product->calculated_hpp = (int)round($calculatedHpp) ?? 0;
            $product->save();
        }
    }

    /**
     * Helper function to grab API token for API documentation page.
     */
    public function getToken(): string
    {
        if (App::environment('production')) abort(403);

        $authType = request()->query('auth');

        if ($authType) {
            try {
                $type = UserType::fromValue($authType);
            } catch (Exception) {
                // sales as default
                $type = UserType::SALES();
            }
        } else {
            $type = UserType::SALES();
        }

        $user   = User::where('type', $type->value)->firstOrFail();
        $tokens = $user->tokens;

        return $tokens->isEmpty() ? $user->createToken('default')->plainTextToken : $tokens->first()->plain_text_token;
    }

    /**
     * @param Builder $query
     * @return Response|BinaryFileResponse
     */
    public function genericModelExport(Builder $query, string $table,): mixed
    {
        // class must be BaseModel
        return (new ModelExport($query, $table))->download('export.csv');
    }

    /**
     * Checks whether currently logged in user has the
     * privilege to assign an unhandled lead.
     * @return bool
     */
    public function loggedInUserCanAssignLead(): bool
    {
        $user = user();

        if ($user->is_admin || $user->is_director) {
            return true;
        }

        if ($user->is_supervisor && $user->supervisorType->can_assign_lead) {
            return true;
        }

        return false;
    }

    /**
     * Assign an unhandled lead to an user
     * @param Lead $lead
     * @param User $user
     * @throws Exception
     */
    public function assignLeadToUser(Lead $lead, User $user): void
    {
        // check that lead is still unhandled
        if (!$lead->is_unhandled) {
            throw new Exception('Attempting to assign lead that is already handled not allowed.');
        }

        if ($lead->user_id == $user->id) {
            throw new Exception('Lead already taken by you.');
        }

        // check permission
        // if (!$this->loggedInUserCanAssignLead()) {
        if (!$this->loggedInUserCanAssignLead() && $user->type->isNot(UserType::SALES)) {
            throw new Exception('This user does not have privilege to assign an unhandled lead.', 403);
        }
        // if ($lead->company_id !== $user->company_id) {
        //     throw new Exception("User {$user->name} does not belong to the same company as lead {$lead->label}");
        // }

        // $channel_id = User::findOrFail($user->id)->getDefaultChannel();
        $channel_id = $user->getDefaultChannel();

        if (!$channel_id || $channel_id == null) {
            $channel_id = $lead->channel_id;
        }

        $lead->update(
            [
                'user_id' => $user->id,
                'is_unhandled' => $user->type->is(UserType::SALES) ? false : true,
                'channel_id' => $channel_id,
            ]
        );

        // notify user (new lead assigned)
        if (isset($user->notificationDevices) && count($user->notificationDevices) > 0) {
            $type = NotificationType::NewLeadAssigned();
            $link = config("notification-link.{$type->key}") ?? 'no-link';

            $message = ExpoMessage::create()
                ->addRecipients($user)
                ->setBadgeFor($user)
                ->title("New Assigned Lead")
                ->body($lead->label . ' has been assigned to your lead list.')
                ->code($type->key)
                ->link($link);

            app(PushNotificationService::class)->notify($message);
        }
    }

    public static function createVoucher(string $id, int $value, $companyId): void
    {
        Voucher::firstOrCreate(
            ['id' => $id],
            [
                'value' => $value,
                'company_id' => $companyId
            ]
        );
    }

    public static function createAndAssignVoucher(Customer $customer, array $vouchers, $companyId = null): void
    {
        foreach ($vouchers as $v) {
            self::createVoucher($v['id'], $v['value'], $companyId ?? user()->company_id);

            if ($customer->vouchers()->where('id', $v['id'])->doesntExist()) {
                $customer->vouchers()->attach($v['id'], [
                    'is_used' => 0,
                ]);
            }
        }
    }
}
