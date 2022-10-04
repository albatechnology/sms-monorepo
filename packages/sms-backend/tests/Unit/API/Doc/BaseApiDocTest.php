<?php

namespace Tests\Unit\API\Doc;


use App\Http\Middleware\Authenticate;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Osteel\OpenApi\Testing\ResponseValidatorBuilder;
use Tests\TestCase;

/**
 * Class ChannelDocTest
 * @package Tests\Unit\API
 */
abstract class BaseApiDocTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->sales()->withChannel()->create();
    }

    public function makeApiTest($path, $method, $body = [], $debug = false)
    {
        $this->withoutMiddleware(Authenticate::class);

        $request = $this->withHeaders(['Accept' => 'application/json']);

        if($this->user) {
            Sanctum::actingAs($this->user, ['*']);
        }

        $response = null;

        try{
            $response = $request->$method($path, empty($body) ? [] : $body);

        }catch(Exception $e){
            if ($debug) {
                dd($e->getMessage());
            }else{
                throw $e;
            }
        }



        $file = config('filesystems.disks.open-api.root').'//'.config('core.open_api.filename');
        $validator = ResponseValidatorBuilder::fromJson($file)->getValidator();

        try{

            $result = $validator->validate($path, $method, $response->baseResponse);
            $this->assertTrue($result);
        }catch (Exception $e){
            if($debug) dd($response->json());
            throw $e;
        }

    }
}
