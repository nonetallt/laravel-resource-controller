<?php

namespace Nonetallt\LaravelResourceController\Testing;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Nonetallt\LaravelResourceController\ResourceControllerAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase;

abstract class ResourceControllerTest extends TestCase
{
    protected $modelClass;
    protected $resource;

    public function setUp() : void
    {
        parent::setUp();
        $this->modelClass = $this->getModelClass();
        $this->resource = Str::snake(Str::afterLast($this->modelClass, '\\'));

        foreach(array_column(ResourceControllerAction::cases(), 'value') as $action) {
            $prefix = 'test' . ucfirst($action);

            if(! str_starts_with($this->getName(), $prefix)) {
                continue;
            }

            $this->skipTestIfActionIsExcluded($action);
        }
    }


    public function testIndexReturnsOkay()
    {
        $this->actingAs($this->getAuthenticatable())->get(route("$this->resource.index"))->assertOk();
    }

    public function testCreateReturnsOk()
    {
        $this->actingAs($this->getAuthenticatable())->get(route("$this->resource.create", $this->getCreateProps()))->assertOk();
    }

    public function testShowReturnsForbiddenIfResourceDoesNotBelongToAuthenticatable()
    {
        $model = ($this->modelClass)::factory()->make();
        $model->save();

        $this->actingAs($this->getAuthenticatable())->get(route("$this->resource.show", $model->id))->assertForbidden();
    }

    public function testShowReturnsOkayIfResourceBelongsToAuthenticatable()
    {
        $model = $this->createModel($this->getAuthenticatable());
        $model->save();

        $this->actingAs($this->getAuthenticatable())->get(route("$this->resource.show", $model->id))->assertOk();
    }

    public function testEditReturnsForbiddenIfResourceDoesNotBelongToAuthenticatable()
    {
        $model = ($this->modelClass)::factory()->make();
        $model->save();

        $this->actingAs($this->getAuthenticatable())->get(route("$this->resource.edit", $model->id))->assertForbidden();
    }

    public function testEditReturnsOkayIfResourceBelongsToAuthenticatable()
    {
        $model = $this->createModel($this->getAuthenticatable());
        $model->save();

        $this->actingAs($this->getAuthenticatable())->get(route("$this->resource.edit", $model->id))->assertOk();
    }

    public function testStoreRequiresAuth()
    {
        $this->store([])->assertRedirectToRoute('login');
    }

    public function testStoreSavesModel()
    {
        $data = $this->getStoreData();
        $response = $this->store($data, $this->getAuthenticatable());
        $response->assertSessionHasNoErrors();

        $key = array_key_first($data);
        $response->assertValid();
        $this->assertTrue(($this->modelClass)::where($key, $data[$key])->exists());
    }

    public function testStoreRedirectsCorrectly()
    {
        $response = $this->store($this->getStoreData(), $this->getAuthenticatable());
        $response->assertValid();
        $response->assertRedirect($this->expectedRedirection(ResourceControllerAction::Store, $this->getLatestModel()));
    }

    public function testUpdateRequiresAuth()
    {
        $model = $this->createModel();
        $model->save();

        $this->update($model->id, [])->assertRedirectToRoute('login');
    }

    public function testUpdateUpdatesModel()
    {
        $model = $this->createModel($this->getAuthenticatable());
        $model->save();
        $model->unsetRelations();
        $model->fill($this->createModel()->toArray());

        $updates = array_filter($model->toArray(), fn($key) => $model->isFillable($key), ARRAY_FILTER_USE_KEY);

        $response = $this->update($model->id, $updates, $this->getAuthenticatable());
        $response->assertValid();

        $this->assertEquals($model->toArray(), array_intersect_key($model->toArray(), $this->getLatestModel()->toArray()));
    }

    public function testUpdateRedirectsCorrectly()
    {
        $model = $this->createModel($this->getAuthenticatable());
        $model->save();
        $response = $this->update($model->id, $model->toArray(), $this->getAuthenticatable());

        $response->assertValid();
        $response->assertRedirect($this->expectedRedirection(ResourceControllerAction::Update, $model));
    }

    public function testDestroyRequiresAuth()
    {
        $model = $this->createModel();
        $model->save();

        $response = $this->delete($this->getRoute(ResourceControllerAction::Destroy, $model->id));
        $response->assertRedirectToRoute('login');
    }

    public function testDestroyDeletesModel()
    {
        $model = $this->createModel($this->getAuthenticatable());
        $model->save();

        $response = $this->actingAs($this->getAuthenticatable())->delete($this->getRoute(ResourceControllerAction::Destroy, $model->id));
        $response->assertValid();

        $this->assertNull(($this->modelClass)::find($model->id));
    }

    public function testDestroyResultsInForbiddenIfTheAuthenticatableDoesNotOwnTheResource()
    {
        $model = $this->createModel();
        $model->save();

        $response = $this->actingAs($this->getAuthenticatable())->delete($this->getRoute(ResourceControllerAction::Destroy, $model->id));
        $response->assertForbidden();

        $this->assertNotNull(($this->modelClass)::find($model->id));
    }

    private function skipTestIfActionIsExcluded(string $action)
    {
        $shouldTest = in_array($action, array_diff($this->includeActions(), $this->excludeActions()));

        if(! $shouldTest) {
            $class = static::class;
            $this->markTestSkipped("$class::$action() not included in resource controller tests.");
        }
    }

    protected function getFactory() : Factory
    {
        return ($this->modelClass)::factory();
    }

    protected function createModel(Model|Factory|Authenticatable ...$recycledModels) : mixed
    {
        $factory = $this->getFactory();

        foreach($recycledModels as $model) {
            $factory = $factory->recycle($model);
        }

        return $factory->make();
    }

    protected function store(array $data, Authenticatable $authenticatable = null)
    {
        $route = $this->getRoute(ResourceControllerAction::Store);

        if($authenticatable !== null) {
            return $this->actingAs($authenticatable)->post($route, $data);
        }

        return $this->post($route, $data);
    }

    protected function update(int|string $id, array $data, Authenticatable $authenticatable = null)
    {
        $route = $this->getRoute(ResourceControllerAction::Update, $id);

        if($authenticatable !== null) {
            return $this->actingAs($authenticatable)->patch($route, $data);
        }

        return $this->patch($route, $data);
    }

    protected function getRoute(ResourceControllerAction $action, $data = null) : string
    {
        return route("$this->resource.$action->value", $data);
    }

    protected function getStoreData() : array
    {
        return $this->createModel($this->getAuthenticatable())->toArray();
    }

    protected function excludeActions() : array
    {
        return [];
    }

    protected function includeActions() : array
    {
        return array_column(ResourceControllerAction::cases(), 'value');
    }

    protected function expectedRedirection(ResourceControllerAction $action, Model $model) : string
    {
        $modelShortName = Str::afterLast($this->modelClass, '\\');
        $controllerClass = "App\\Http\\Controllers\\{$modelShortName}Controller";
        $destinationUrl = (new $controllerClass)->getRedirection($action, $model)->getTargetUrl();

        return $destinationUrl;
    }

    protected function getLatestModel() : ?Model
    {
        $query = ($this->modelClass)::query();

        if((new ($this->modelClass))->incrementing) {
            $query->orderBy('id', 'desc');
        }
        else {
            $query->latest();
        }

        return $query->first();
    }

    abstract protected function getCreateProps() : array;

    abstract protected function getModelClass() : string;

    abstract protected function getAuthenticatable() : Authenticatable;
}
