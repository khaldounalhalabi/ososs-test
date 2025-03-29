<?php

namespace Tests\Feature\Contracts;

use App\Models\User;
use App\Traits\TestHelpers;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MainTestCase extends TestCase
{
    use TestHelpers;

    /**
     * @param array $additionalFactoryData optional data to the factories
     * @param bool  $ownership             determine if the action has to be on the authenticated user data
     */
    public function deleteTest(array $additionalFactoryData = [], bool $ownership = false, bool $isDebug = false): void
    {
        // the user provided undefined ID
        $this->failedSingleResponse();

        $this->delete(route($this->requestPath, fake()->randomNumber()), [], $this->headers)
            ->assertExactJson($this->responseBody)
            ->assertNotFound();

        if ($ownership) {
            // the user tried to delete another user data
            $array = array_merge($additionalFactoryData, ['user_id' => User::factory()->create()->id]);
            $factoryData = $this->model::factory()->create($array);

            $this->delete(route($this->requestPath, $factoryData->id), [], $this->headers)
                ->assertExactJson($this->responseBody)
                ->assertOk();

            // the user tried to delete his data
            $array = array_merge($additionalFactoryData, ['user_id' => auth()->user()->id]);
            $factoryData = $this->model::factory()->create($array);
        } else {
            $factoryData = $this->model::factory()->create($additionalFactoryData);
        }

        $this->responseBody['data'] = null;
        $this->deleteSuccess();

        $response = $this->delete(route($this->requestPath, $factoryData->id), [], $this->headers)
            ->assertExactJson($this->responseBody)
            ->assertOk();

        if ($this->checkSoftDeleteColumn()) {
            $this->assertSoftDeleted($factoryData);
        } else {
            $this->assertModelMissing($factoryData);
        }

        if ($isDebug) {
            dd($response);
        }
    }

    /**
     * @param array $additionalFactoryData optional data to the factories
     * @param bool  $ownership             determine if the action has to be on the authenticated user data
     */
    public function indexTest(array $additionalFactoryData = [], bool $ownership = false, bool $isDebug = false): void
    {
        // there is no data
        $this->failedMultiResponse();

        $this->get(route($this->requestPath), $this->headers)
            ->assertExactJson($this->responseBody)
            ->assertOk();

        if ($ownership) {
            // the user tried to index another user data
            $array = array_merge($additionalFactoryData, ['user_id' => User::factory()->create()->id]);
            $this->model::factory(5)->create($array);

            $this->get(route($this->requestPath), $this->headers)
                ->assertExactJson($this->responseBody)
                ->assertOk();

            // the user tried to index his own data
            $array = array_merge($additionalFactoryData, ['user_id' => auth()->user()->id]);
            $factoryData = $this->model::factory(5)->create($array);
        } else {
            // data exists
            $factoryData = $this->model::factory(5)->create($additionalFactoryData);
        }

        $factoryData->each->refresh();

        $this->responseBody['data'] = $this->convertResourceToArray($factoryData->load($this->relations), true);
        $this->responseBody['message'] = __('site.get_successfully');
        $this->responseBody['pagination_data'] = $this->pagination;
        $response = $this->get(route($this->requestPath), $this->headers)
            ->assertExactJson($this->responseBody)
            ->assertOk();
        if ($isDebug) {
            dd($response);
        }
    }

    /**
     * @param array $additionalFactoryData optional data to the factories
     * @param bool  $ownership             determine if the action has to be on the authenticated user data
     */
    public function showTest(array $additionalFactoryData = [], bool $ownership = false, bool $isDebug = false): void
    {
        // the user provided invalid id
        $this->failedSingleResponse();

        $this->get(route($this->requestPath, fake()->uuid()), $this->headers)
            ->assertExactJson($this->responseBody)
            ->assertNotFound();

        if ($ownership) {
            // the user tried to show another user data
            $array = array_merge($additionalFactoryData, ['user_id' => User::factory()->create()->id]);
            $factoryData = $this->model::factory()->create($array);

            $this->get(route($this->requestPath, $factoryData->id), $this->headers)
                ->assertExactJson($this->responseBody)
                ->assertOk();

            // the user tried to show his own data
            $array = array_merge($additionalFactoryData, ['user_id' => auth()->user()->id]);
            $factoryData = $this->model::factory()->create($array);
        } else {
            $factoryData = $this->model::factory()->create($additionalFactoryData);
        }

        // the user provided the right id
        $this->responseBody['data'] = $this->convertResourceToArray($factoryData->refresh()->load($this->relations));
        $this->responseBody['message'] = __('site.get_successfully');
        $this->responseBody['code'] = Response::HTTP_OK;
        $response = $this->get(route($this->requestPath, $factoryData->id), $this->headers)
            ->assertExactJson($this->responseBody)
            ->assertOk();

        if ($isDebug) {
            dd($response);
        }
    }

    /**
     * @param array $additionalAttributes optional data to the factories
     */
    public function storeTest(array $additionalAttributes = [], mixed $requestParams = null, bool $isDebug = false): void
    {
        $attributes = $this->model::factory()->raw($additionalAttributes);
        $response = $this->post(route($this->requestPath, $requestParams), $attributes, $this->headers)
            ->assertOk();

        if ($isDebug) {
            dd($response);
        }

        $createdModel = $this->model::orderByDesc('id')->first();

        $this->responseBody['data'] = $this->convertResourceToArray($createdModel->load($this->relations));
        $this->responseBody['message'] = __('site.stored_successfully');

        $response->assertExactJson($this->responseBody)
            ->assertOk();

        $this->assertModelExists($createdModel);
    }

    /**
     * @param array $additionalFactoryData optional data to the factories
     * @param array $attributes            if you are trying to send a custom attributes to the update request send an
     *                                     array of it
     * @param bool  $ownership             determine if the action has to be on the authenticated user data
     * @param bool  $replacing             this var pointing to the case where the update endpoint creating a new
     *                                     record to the database
     */
    public function updateTest(array $attributes = [], array $additionalFactoryData = [], bool $ownership = false, bool $replacing = true, bool $isDebug = false): void
    {
        $attributes = $this->model::factory()->raw($attributes);
        //the user provided invalid ID
        $this->failedSingleResponse();
        $this->put(route($this->requestPath, fake()->uuid), $attributes, $this->headers)
            ->assertExactJson($this->responseBody)
            ->assertNotFound();

        if ($ownership) {
            // the user tried to update another user data
            $array = array_merge($additionalFactoryData, ['user_id' => User::factory()->create()->id]);
            $factoryData = $this->model::factory()->create($array);

            $this->get(route($this->requestPath, $factoryData->id), $this->headers)
                ->assertExactJson($this->responseBody)
                ->assertOk();

            // the user tried to update his data
            $array = array_merge($additionalFactoryData, ['user_id' => auth()->user()->id]);
            $factoryData = $this->model::factory()->create($array);
        } else {
            $factoryData = $this->model::factory()->create($additionalFactoryData);
        }

        //the user provided the right ID
        $response = $this->put(route($this->requestPath, $factoryData->id), $attributes, $this->headers)->assertOk();
        if ($replacing) {
            $factoryData->refresh();
            $this->assertModelExists($factoryData);
            $this->responseBody['data'] = $this->convertResourceToArray($factoryData->load($this->relations));
        } else {
            $createdData = $this->model::query()->orderByDesc('id')->first();
            $this->assertModelExists($createdData);
            $this->responseBody['data'] = $this->convertResourceToArray($createdData->load($this->relations));
        }

        $this->updateSuccess();
        $response->assertExactJson($this->responseBody);

        if ($isDebug) {
            dd($response);
        }
    }

    public function post($uri, array $data = [], array $headers = []): TestResponse
    {
        return parent::post($uri, $data, array_merge($this->headers, $headers));
    }

    public function get($uri, array $headers = []): TestResponse
    {
        return parent::get($uri, array_merge($this->headers, $headers));
    }

    public function put($uri, array $data = [], array $headers = []): TestResponse
    {
        return parent::put($uri, $data, array_merge($this->headers, $headers));
    }

    public function delete($uri, array $data = [], array $headers = []): TestResponse
    {
        return parent::delete($uri, headers: array_merge($this->headers, $headers));
    }
}
