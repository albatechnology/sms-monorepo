<?php

namespace App\Classes;

use App\Classes\DocGenerator\Enums\DataFormat;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Enums\AddressType;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\NotificationType;
use App\Enums\OrderPaymentStatus;
use App\Enums\ProductCategoryType;
use App\Enums\ReportableType;
use App\Enums\TargetType;
use App\Enums\UserType;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\Address;
use App\Models\CartDemand;
use App\Models\Channel;
use App\Models\Colour;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\Covering;
use App\Models\Customer;
use App\Models\CustomerDeposit;
use App\Models\CustomerVoucher;
use App\Models\Discount;
use App\Models\InteriorDesign;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductTag;
use App\Models\ProductUnit;
use App\Models\ProductVersion;
use App\Models\Promo;
use App\Models\PromoCategory;
use App\Models\QaMessage;
use App\Models\QaTopic;
use App\Models\Report;
use App\Models\Stock;
use App\Models\SubLeadCategory;
use App\Models\SupervisorType;
use App\Models\Target;
use App\Models\User;
use App\Models\Voucher;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CustomQueryBuilder extends QueryBuilder
{
    const DEFAULT_PER_PAGE = 15;
    const MAX_PER_PAGE     = 100;
    const MIN_PER_PAGE     = 1;

    const TYPE_EXACT = 'exact';
    const TYPE_SCOPE = 'scope';
    const TYPE_ENUM  = 'enum';

    const KEY_ID_NAME = 'GetIdName';

    public static function getKeys($class)
    {
        return collect(self::getAllowedFilterMap()[$class])->pluck('key')->toArray();
    }

    public static function getAllowedFilterMap($resource = null)
    {
        $data = [

            self::KEY_ID_NAME => [
                self::ids(),
                self::string('name', 'Name'),
            ],
            Address::class      => [
                self::ids(),
                self::id('customer_id'),
                self::string('address_line_1', 'address line 1'),
                self::string('address_line_2', 'address line 2'),
                self::string('city', 'Jakarta'),
                self::string('country', 'indonesia'),
                self::string('province', 'Jawa Barat'),
                self::string('phone', '312213213'),
                self::enum('type', AddressType::class),
            ],
            Colour::class       => [
                self::ids(),
                self::string('name', 'white'),
                self::string('description', '54h8'),
                self::id('product_id'),
            ],
            Covering::class     => [
                self::ids(),
                self::id('product_id'),
                self::string('name', 'Product name'),
                self::string('type', 'Soft cover'),
            ],
            Notification::class => [
                self::enum('type', NotificationType::class),
            ],
            Order::class => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                // self::scope('customer_name', 'customerName', 'Customer A'),
                self::scope('search', 'customerNameAndInvoiceNumber', 'Search By Customer Name or Invoice Number'),
                self::string('invoice_number', 'INV2020012837'),
                self::enum('approval_status', \App\Enums\OrderApprovalStatus::class),
                // [
                //     'key'         => 'approval_send_to_me',
                //     'alias'       => 'approvalSendToMe',
                //     'type'        => self::TYPE_SCOPE,
                //     'data_format' => DataFormat::BOOLEAN,
                //     'schema'      => Schema::boolean('approval_send_to_me')->example(true),
                // ],
            ],
            OrderDetail::class => [
                self::ids(),
            ],
            Payment::class => [
                self::ids(),
                self::number('amount'),
                self::string('reference', 'test-payment'),
                self::enum('status', OrderPaymentStatus::class),
                self::ids('payment_type_id', 'wherePaymentTypeId'),
                self::ids('added_by_id', 'whereAddedById'),
                self::ids('approved_by_id', 'whereApprovedById'),
                self::ids('order_id', 'whereOrderId'),
            ],
            PaymentCategory::class => [
                self::ids(),
                self::string('name', 'Virtual Account'),
            ],
            PaymentType::class => [
                self::ids(),
                self::ids('payment_category_id', 'wherePaymentCategoryId'),
                self::string('name', 'Bank BNI'),
            ],
            ProductModel::class        => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::string('name', 'Product name'),
            ],
            ProductVersion::class      => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::ids('product_model_id', 'whereProductModelId'),
            ],
            ProductCategoryCode::class => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::ids('product_model_id', 'whereProductModelId'),
                self::ids('product_version_id', 'whereProductVersionId'),
            ],
            Product::class             => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::ids('product_category_id', 'whereProductCategoryId'),
                // self::ids('product_brand_id', 'whereProductBrandId'),
                // self::ids('product_model_id', 'whereProductModelId'),
                // self::ids('product_version_id', 'whereProductVersionId'),
                // self::ids('product_category_code_id'),
                self::scope('name', 'whereNameSearch', 'Product ABC', 'Search by product name'),
                self::scope('tags', 'whereTags', 'tag1', 'Searches product by tags'),
            ],
            ProductUnit::class         => [
                self::ids(),
                self::id('product_id'),
                self::id('colour_id'),
                self::id('covering_id'),
                self::scope('name', 'whereNameSearch', 'Product ABC', 'Search by product name'),
            ],
            Report::class              => [
                self::ids(),
                self::scope('period_before', 'periodBefore', ApiDataExample::TIMESTAMP),
                self::scope('period_after', 'periodAfter', ApiDataExample::TIMESTAMP),
            ],
            Stock::class               => [
                self::ids(),
                self::ids('channel_id', 'whereChannelId'),
                self::ids('product_unit_id', 'whereProductUnitId'),
            ],
            Target::class              => [
                self::ids(),
                self::id('report_id'),
                self::scope('is_dashboard', null, 1, 'fill 1 in dashboard screen', schemaType: 'integer'),
                self::scope('start_after', 'startAfter', ApiDataExample::TIMESTAMP),
                self::scope('end_before', 'endBefore', ApiDataExample::TIMESTAMP),
                [
                    'key'         => 'reportable_type',
                    'alias'       => 'whereReportableType',
                    'type'        => self::TYPE_SCOPE,
                    'data_format' => DataFormat::ENUM,
                    'enum_class'  => ReportableType::class,
                    'schema'      => Schema::string('reportable_type')
                        ->example(ReportableType::getDefaultInstance()->key)
                        ->enum(...ReportableType::getKeys())
                        ->description('Filter by reportable type.'),
                ],
                self::scope('reportable_ids', 'whereReportableIds', '1,2,3', 'Searches reportable by ids. Allow multiple values separated by comma'),
                self::enum('type', TargetType::class),
                self::scope(
                    'supervisor_type_level',
                    'whereSupervisorTypeLevel',
                    1,
                    'Filter by supervisor type level. Use -1 to filter by sales only',
                    schemaType: 'integer'
                ),
                self::scope(
                    'company_id',
                    'whereCompanyId',
                    1,
                    'Filter by company id',
                    schemaType: 'integer'
                ),
                self::scope(
                    'descendant_of',
                    'whereDescendantOf',
                    1,
                    'Filter to show only the target from user supervised by the given user id',
                    schemaType: 'integer'
                ),
            ],
            Customer::class            => [
                self::ids(),
                self::string('first_name', 'Barrack'),
                self::string('last_name', 'Obama'),
                self::string('email', ApiDataExample::EMAIL),
                self::string('phone', '083123123'),
                self::scope('search', 'whereSearch', 'test@test.com', 'Searches name, email and phone.'),
                [
                    'key'    => 'has_activity',
                    'schema' => Schema::boolean('has_activity')->example(true),
                ],
            ],
            Voucher::class            => [
                self::scope('id', 'whereCodeLike', 'GratisOngkir'),
                self::id('company_id'),
                self::scope('start_time', 'whereStartTimeAfter', ApiDataExample::DATE),
                self::scope('end_time', 'whereEndTimeBefore', ApiDataExample::DATE),
                [
                    'key'    => 'is_active',
                    'data_format' => DataFormat::BOOLEAN,
                    'schema' => Schema::boolean('is_active')->example(true),
                ],
            ],
            CustomerVoucher::class            => [
                self::id('customer_id'),
                self::scope('lead_id', 'whereLeadId', 1),
                self::scope('company_id', 'whereCompanyId', 1),
                self::scope('voucher_id', 'whereCodeLike', 'GratisOngkir'),
                self::scope('start_time', 'whereStartTimeAfter', ApiDataExample::DATE),
                self::scope('end_time', 'whereEndTimeBefore', ApiDataExample::DATE),
                [
                    'key'    => 'is_used',
                    'schema' => Schema::boolean('is_used')->example(true),
                ],
                [
                    'key'         => 'is_active',
                    'alias'       => 'whereIsActive',
                    'type'        => self::TYPE_SCOPE,
                    'data_format' => DataFormat::BOOLEAN,
                    'schema'      => Schema::boolean('is_active')->example(true),
                ],
            ],
            CustomerDeposit::class            => [
                self::ids(),
            ],
            User::class                => [
                self::ids(),
                self::id('channel_id', 'whereChannelId'),
                self::ids('supervisor_id', 'whereSupervisorId'),
                self::ids('supervisor_type_id', 'whereSupervisorTypeId'),
                self::id('company_id'),
                self::string('name', 'Barrack obama'),
                self::enum('type', UserType::class),
                self::scope(
                    'descendant_of',
                    'customDescendantOf',
                    1,
                    'Filter to show only the descendant from user by the given user id',
                    schemaType: 'integer'
                ),
            ],
            Channel::class             => [
                self::ids(),
                self::id('company_id'),
                self::string('name', 'Toko ABC'),
                self::scope('supervisor_id', 'whereSupervisorId'),
            ],
            ProductBrand::class             => [
                self::ids(),
                self::id('company_id'),
                self::string('name', 'Toko ABC'),
            ],
            Discount::class            => [
                self::ids(),
                // self::string('name', 'Discount ABC'),
                // self::string('activation_code', 'ABCDE'),
                // self::scope('search', 'customSearch', 'Diskon 50%'),
                self::scope('name', 'whereNameLike', 'Diskon 50%'),
                self::scope('activation_code', 'whereActivationCodeLike', 'ABCDE'),
            ],
            Lead::class                => [
                self::ids(),
                self::id('user_id'),
                self::enum('type', LeadType::class),
                self::enum('status', LeadStatus::class),
                self::string('label', 'my lead'),
                self::id('lead_category_id', 'whereLeadCategoryId'),
                self::id('sub_lead_category_id', 'whereSubLeadCategoryId'),
                // self::id('product_brand_id', 'whereProductBrandId'),
                // self::scope('user_name', 'userName', 'Difa Supervisor'),
                self::scope('customer_name', 'customerName', 'Customer A'),
                self::scope('customer_search', 'customerSearch', 'Customer A', 'Search by customer name, email and phone'),
                self::scope('channel_name', 'channelName', 'Channel A'),
                [
                    'key'         => 'is_new_customer',
                    'type'        => self::TYPE_EXACT,
                    'data_format' => DataFormat::BOOLEAN,
                    'schema'      => Schema::boolean('is_new_customer')->example(true),
                ],
                [
                    'key'    => 'has_activity',
                    'schema' => Schema::boolean('has_activity')->example(true),
                ],
                [
                    'key'         => 'customer_has_activity',
                    'alias'       => 'customerHasActivity',
                    'type'        => self::TYPE_SCOPE,
                    'data_format' => DataFormat::BOOLEAN,
                    'schema'      => Schema::boolean('customer_has_activity')
                        ->description('Whether the customer of this lead has any activity')
                        ->example(true),
                ],
            ],
            LeadCategory::class        => [
                self::ids(),
                self::string('name', 'category'),
                self::string('description', 'category'),
            ],
            SubLeadCategory::class        => [
                self::ids(),
                self::string('name', 'category'),
                self::string('description', 'category'),
            ],
            Activity::class        => [
                self::ids(),
                self::id('order_id'),
                self::ids('user_id', 'whereUserId'),
                self::ids('customer_id', 'whereCustomerId'),
                self::ids('channel_id', 'whereChannelId'),
                self::scope('company_id', 'whereCompanyId', schema: Schema::integer('company_id')->example(1)),
                self::enum('follow_up_method', ActivityFollowUpMethod::class),
                self::enum('status', ActivityStatus::class),
                self::string('feedback', 'my activity'),
                self::scope(
                    'target_id',
                    'whereTargetId',
                    1,
                    'Target must be of ACTIVITY_COUNT type',
                    schema: Schema::integer('target_id')->example(1),
                ),
                self::scope('follow_up_datetime_before', 'followUpDatetimeBefore', ApiDataExample::TIMESTAMP),
                self::scope('follow_up_datetime_after', 'followUpDatetimeAfter', ApiDataExample::TIMESTAMP),
                self::scope('has_payment', 'whereHasPayment', schema: Schema::boolean('has_payment')->example(true)),
                self::ids('has_any_brands', 'whereHasAnyBrands'),
                self::scope('created_before', 'createdBefore', ApiDataExample::TIMESTAMP),
                self::scope('created_after', 'createdAfter', ApiDataExample::TIMESTAMP),
            ],
            Company::class         => [
                self::ids(),
                self::string('name', 'test company'),
            ],
            InteriorDesign::class         => [
                self::ids(),
                self::id('religion_id'),
                self::string('name', 'Interior Design Alam Sutera'),
            ],
            CompanyAccount::class  => [
                self::ids(),
                self::string('name', 'test account'),
            ],
            ActivityComment::class => [
                self::ids(),
                self::id('user_id'),
                self::id('activity_id'),
                self::id('activity_comment_id', 'The parent comment if this comment is a reply comment'),
                self::string('content', 'comment content'),
            ],
            ProductTag::class      => [
                self::ids(),
                self::string('name', 'test tag'),
                self::string('slug', 'test-tag'),
            ],
            ProductCategory::class => [
                self::ids(),
                self::string('name', 'test category'),
                self::string('description'),
                // self::enum('type', ProductCategoryType::class),
                // self::id('parent_id'),
            ],
            Promo::class           => [
                self::ids(),
                self::id('promo_category_id'),
                self::scope('start_after', 'startAfter', ApiDataExample::TIMESTAMP),
                self::scope('end_before', 'endBefore', ApiDataExample::TIMESTAMP),
            ],
            PromoCategory::class => [
                self::ids(),
                self::string('name', 'Promo Category'),
            ],
            QaTopic::class         => [
                self::ids(),
                self::string('subject'),
                self::id('creator_id'),
            ],
            QaMessage::class       => [
                self::ids(),
                self::string('content'),
                self::ids('topic_id'),
                self::ids('sender_id'),
                [
                    'key'         => 'is_unread',
                    'alias'       => 'isUnread',
                    'type'        => self::TYPE_SCOPE,
                    'data_format' => DataFormat::BOOLEAN,
                    'schema'      => Schema::boolean('is_unread')->example(true),
                ],
            ],
            SupervisorType::class  => [
                self::ids(),
            ],
            CartDemand::class  => [
                self::ids(),
            ],
        ];

        return $resource ? $data[$resource] : $data;
    }

    // region Helper Class
    protected static function ids($key = 'id', string $scopeAlias = null)
    {
        if (is_null($scopeAlias)) {
            return [
                'key'         => $key,
                'type'        => self::TYPE_EXACT,
                'schema'      => Schema::string($key)->example('1,2,3')->description('Set of ids, comma separated'),
                'data_format' => DataFormat::CSV
            ];
        } else {
            return [
                'key'         => $key,
                'alias'       => $scopeAlias,
                'type'        => self::TYPE_SCOPE,
                'schema'      => Schema::string($key)->example('1,2,3')->description('Set of ids, comma separated'),
                'data_format' => DataFormat::CSV
            ];
        }
    }

    protected static function string($key, $example = null, $description = null)
    {
        $example = $example ?? 'test ' . $key;
        $schema  = Schema::string($key)->example($example);

        if ($description) $schema = $schema->description($description);

        return [
            'key'    => $key,
            'schema' => $schema,
        ];
    }

    protected static function id($key = 'id', $description = null)
    {
        $schema = Schema::integer($key)->example(1);
        if ($description) $schema = $schema->description($description);

        return [
            'key'         => $key,
            'type'        => self::TYPE_EXACT,
            'schema'      => $schema,
            'data_format' => DataFormat::NUMERIC
        ];
    }

    protected static function enum($key, $enum_class, $description = null)
    {
        // For enums, we provide the enum key to the API instead of value
        // So here we need to take the key and transform it to enum value

        return [
            'key'         => $key,
            'type'        => self::TYPE_ENUM,
            'data_format' => DataFormat::ENUM,
            'enum_class'  => $enum_class,
            'schema'      => Schema::string($key)
                ->example($enum_class::getDefaultInstance()->key)
                ->enum(...$enum_class::getKeys())
                ->description($description),
        ];
    }

    protected static function number($key, $example = null, $description = null)
    {
        $example = $example ?? 1;
        $schema  = Schema::integer($key)->example($example);

        if ($description) $schema = $schema->description($description);

        return [
            'key'    => $key,
            'schema' => $schema,
        ];
    }

    protected static function scope(
        $key,
        $alias = null,
        $example = null,
        $description = null,
        $schema = null,
        $schemaType = 'string',
    ) {
        return [
            'key'    => $key,
            'alias'  => $alias ?? $key,
            'type'   => self::TYPE_SCOPE,
            'schema' => $schema ?? Schema::$schemaType($key)->example($example ?? 'test ' . $key)->description($description),
        ];
    }

    //endregion

    public static function getSchemas($class)
    {
        return collect(self::getAllowedFilterMap()[$class])->pluck('schema')->toArray();
    }

    public static function buildResource(string $model_class, string $resource_class, callable $closure = null, string $filter_key = null, bool $useDefaultSort = true)
    {
        $query = $resource_class::collection(self::build($model_class, $closure, $resource_class, $filter_key, $useDefaultSort));
        $query->additional(self::getQueryMetadata($filter_key ?? $model_class, $resource_class));
        return $query;
    }

    public static function build(string $class, $closure = null, string $resource_class = null, string $filter_key = null, bool $useDefaultSort = true)
    {
        $query = self::for($class)->allowedFilters(self::makeAllowedFilters($filter_key ?? $class));
        if ($resource_class) {
            $sortables = self::getSortableFields($resource_class);
            if (!empty($sortables)) $query = $query->allowedSorts(...$sortables);
        }

        if ($useDefaultSort && !request('sort')) {
            $query = $query->orderBy('id', 'desc');
        }

        if ($closure) {
            $query = $closure($query);
        }

        if ($class === Lead::class) {
            return $query->paginate(self::getQueryPerPage());
        }
        return $query->simplePaginate(self::getQueryPerPage());
    }

    public static function makeAllowedFilters($class)
    {
        return collect(self::getAllowedFilterMap()[$class])->map(function ($data) {
            $type = $data['type'] ?? 'partial';

            if ($type == self::TYPE_ENUM) {
                return AllowedFilter::callback($data['key'], function ($query, $values) use ($data) {

                    $values = is_array($values) ? $values : explode(',', $values);

                    $values = collect($values)
                        ->map(function ($value) use ($data) {
                            return $data['enum_class']::fromKey($value)->value;
                        })
                        ->all();

                    $query->whereIn($data['key'], $values);
                });
            }

            if (isset($data['alias'])) {
                return AllowedFilter::$type($data['key'], $data['alias']);
            } else {
                return AllowedFilter::$type($data['key']);
            }
        })->toArray();
    }

    protected static function getSortableFields(string $resource_class)
    {
        return method_exists($resource_class, 'getSortableFields') ? $resource_class::getSortableFields() : [];
    }

    public static function getQueryPerPage()
    {
        $perPage = request('perPage') ?? self::DEFAULT_PER_PAGE;
        return max(self::MIN_PER_PAGE, min(self::MAX_PER_PAGE, (int)$perPage));
    }

    protected static function getQueryMetadata(string $model_class, string $resource_class)
    {
        $filters = collect(self::getAllowedFilterMap($model_class))->map(function ($data) {
            $result = [
                'key'        => $data['key'],
                'dataFormat' => $data['data_format'] ?? DataFormat::DEFAULT
            ];

            if (!empty($data['type']) && $data['type'] === self::TYPE_ENUM) {
                $result['options'] = collect($data['enum_class']::getInstances())->map(function ($data) {
                    return ['value' => $data->key, 'label' => $data->description];
                })->values()->all();
            }

            return $result;
        })->toArray();

        return [
            'query' => [
                'filters' => $filters,
                'sort'    => self::getSortableFields($resource_class)
            ]
        ];
    }

    /**
     * build resource without pagination and sorting
     * @param string $model_class
     * @param string $resource_class
     * @param callable|null $closure
     * @param string|null $filter_key
     * @return mixed
     */
    public static function buildResourceCustom(string $model_class, string $resource_class, callable $closure = null, string $filter_key = null)
    {
        $query = self::for($model_class)->allowedFilters(self::makeAllowedFilters($filter_key ?? $model_class));

        if ($closure) {
            $query = $closure($query);
        }

        $query = $resource_class::collection($query);

        $query->additional(self::getQueryMetadata($filter_key ?? $model_class, $resource_class));
        return $query;
    }
}
