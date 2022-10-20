<?php

namespace App\Services;

use App\Enums\UserType;
use App\Exports\ModelExport;
use App\Models\Activity;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\ProductModel;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Contain methods that may only be executed once to allow for
 * backward compatibility changes, often involving database data changes
 * Class FixerService
 * @package App\Services
 */
class FixerService
{

    /**
     * deal_at property was introduced to
     * @return array
     */
    public static function getEnumContracts(): array
    {
        return collect(Storage::disk('enums')->allFiles())
            // remove php extension
            ->map(fn(string $filename) => (string)Str::of($filename)->before('.'))

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
     * Create an instance of stock of a product for all channel
     * in the company.
     * @param Product $product
     */
    public static function createStocksForProduct(Product $product)
    {
        $ids = Channel::query()
            ->where('company_id', $product->company_id)
            ->pluck('id');

        $existingId = Stock::query()
            ->where('product_id', $product->id)
            ->pluck('id');

        $stockData = $ids->diff($existingId)
            ->map(function (int $id) use ($product) {
                return [
                    'channel_id'      => $id,
                    'product_id' => $product->id,
                    'company_id'      => $product->company_id,
                ];
            });

        Stock::insert($stockData->all());
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
    public function genericModelExport(
        Builder $query,
        string $table,
    )
    {

        // class must be BaseModel
        return (new ModelExport($query, $table))->download('export.csv');
    }

    /**
     * Dispatch async job to fix the price range of productModels
     * @param callable|null $closure
     */
    public function updateProductModelsPriceRange(callable $closure = null): void
    {
        $query = ProductModel::query();

        if ($closure) {
            $query = $closure($query);
        }

        $query->get()->each(function (ProductModel $model){
            dispatch(static function () use ($model) {
                $model->updatePriceRange();
            });
        });
    }

    /**
     * has_activity property is added to Leads. As we have large amount
     * of leads on production, we need to queue this to evaluate whether
     * each leads has activity
     * @param callable|null $closure
     * @param int $chunk
     */
    public function evaluateLeadsHasActivity(callable $closure = null, int $chunk = 100): void
    {
        $query = Lead::query();

        if ($closure) {
            $query = $closure($query);
        }

        $query->chunk($chunk, function($leads) {
            foreach ($leads as $lead) {
                dispatch(static function () use ($lead) {
                    $lead->evaluateHasActivity();
                });
            }
        });
    }

    /**
     * has_activity property is added to Customers. As we have large amount
     * of customers on production, we need to queue this to evaluate whether
     * each customers has activity
     * @param callable|null $closure
     * @param int $chunk
     */
    public function evaluateCustomersHasActivity(callable $closure = null, int $chunk = 100): void
    {
        $query = Customer::query();

        if ($closure) {
            $query = $closure($query);
        }

        $query->chunk($chunk, function($customers) {
            foreach ($customers as $customer) {
                dispatch(static function () use ($customer) {
                    $customer->evaluateHasActivity();
                });
            }
        });
    }
}
