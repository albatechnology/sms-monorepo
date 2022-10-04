<?php

namespace App\Services;

use App\Enums\BaseEnum;
use App\Models\Address;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Str;
use InvalidArgumentException;
use League\Csv\Reader;


class HelperService
{
    use AuthorizesRequests;

    const PRODUCT_NAMES = 'sample_product_name.csv';
    const MODEL_NAMES   = 'sample_model_name.csv';

    public static function normalise(string $string): string
    {
        return (string)Str::of($string)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9]++/', '-');
    }

    /**
     * Load csv sample data as array.
     * Used for seeder
     */
    public static function loadSampleData(string $file): array
    {
        $path = storage_path('data/' . $file);
        $csv  = Reader::createFromPath($path, 'r');
        return collect($csv->getRecords())->collapse()->all();
    }

    /**
     * Temporary method to assign random image to a model.
     * Image are selected in no particular order, but always uses
     * the same image for the same model
     * param Model $model
     * @return Application|UrlGenerator|string
     */
    public static function getDummyImageUrl()
    {
        return url('images/img-not-available.jpg');

        /*
        $images = [
            "Banner.jpg",
            "Collecction_1.jpg",
            "Collecction_2.jpg",
            "Collecction_3.jpg",
            "Collecction_4.jpg",
            "Collecction_5.jpg",
            "collection_6.jpg",
            "New_1.jpg",
            "New_2.jpg",
            "New_3.jpg",
        ];

        $image = $images[$model->id % count($images)];
        return url('images/' . $image);
        */
    }

    /**
     * Takes a model and a possibly nested relationship (separated by ".")
     * and only return the relationship if it has been eager loaded.
     * Otherwise, returns a missing value object
     *
     * @param $resource
     * @param string $property
     */
    public static function getIfLoaded($resource, string $property)
    {
        $relations = collect(explode('.', $property));

        $data = $relations->reduce(function ($carry, $relation) {
            if (is_null($carry)) return null;

            if (!$carry->relationLoaded($relation)) return null;

            return $carry->$relation;
        }, $resource);

        return $data ?? new MissingValue();
    }

    public static function formatRupiah($number)
    {
        return "Rp " . number_format((float)$number, 0, ',', '.');
    }

    public static function getProperty($resource, string $key, $cast = null)
    {
        $data = null;

        if (is_object($resource)) {
            $data = $resource->$key ?? null;
        }

        if (is_array($resource)) {
            $data = $resource[$key] ?? null;
        }

        if (!$data || !$cast) {
            return $data;
        }

        return match ($cast) {
            'int', 'integer' => (int)$data,
            'string' => (string)$data,
            default => $data
        };
    }

    /**
     * Helper method to generate the HTML for the filter column
     * for enum properties. Used in CMS index pages.
     * @param string $baseEnum
     * @return string
     */
    public static function filterByEnum(string $baseEnum): string
    {
        if (!is_a($baseEnum, BaseEnum::class, true)) {
            throw new InvalidArgumentException("{$baseEnum} must be an Enum class.");
        }

        $options = collect($baseEnum::getInstances())->map(function (BaseEnum $enum) {
            return sprintf("<option value='%s'>%s</option>", $enum->value, $enum->description);
        });

        return sprintf(
            "
            <select class='search' strict='true'>
                <option value>%s</option>
                %s
            </select>
            ",
            trans('global.all'),
            $options->implode('\n')
        );
    }


    public static function jsonAddressToString(mixed $address): string
    {
        return Address::make($address)->toString();
    }
}
