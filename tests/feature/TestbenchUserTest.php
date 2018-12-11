<?php
declare(strict_types=1);

namespace Tychovbh\Tests\Mvc\Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tychovbh\Mvc\Repositories\Repository;


use Tychovbh\Tests\Mvc\App\TestUser;
use Tychovbh\Tests\Mvc\App\TestUserRepository;
use Tychovbh\Tests\Mvc\TestCase;

/**
 * @property TestUserRepository repository
 */
class TestUserTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function itCanInstantiate()
    {
        $repository = new TestUserRepository();
        $this->assertInstanceOf(Repository::class, $repository);
        return $repository;
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanListAllUsers(TestUserRepository $repository)
    {
        TestUser::destroy(TestUser::select('id')->get()->toArray());
        $collection = factory(TestUser::class, 10)->create();

        $all = $repository->all();
        $this->assertInstanceOf(Collection::class, $all);
        $this->assertEquals($collection->toArray(), $all->toArray());
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanFilterUsers(TestUserRepository $repository)
    {
        TestUser::destroy(TestUser::select('id')->get()->toArray());
        $user = factory(TestUser::class, 10)->create()->first();
        $all = $repository->where([
            'id' => $user->id
        ])->all();

        $this->assertEquals($user->toArray(), $all->first()->toArray());
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanFilterCollectionOfUsers(TestUserRepository $repository)
    {
        $users = factory(TestUser::class, 10)->create();
        factory(TestUser::class, 10)->create();
        $all = $repository->where([
            'id' => $users->map(function (TestUser $user) {
                return $user->id;
            })->toArray()
        ])->all();

        $this->assertEquals($users->toArray(), $all->toArray());
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanOrderByCollectionOfUsers(TestUserRepository $repository)
    {
        TestUser::destroy(TestUser::select('id')->get()->toArray());
        factory(TestUser::class, 10)->create();
        $users = TestUser::orderBy('id', 'asc')->get();
        $all = $repository->orderBy('id')->all();

        $this->assertEquals($users->toArray(), $all->toArray());
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanPaginateUsers(TestUserRepository $repository)
    {
        TestUser::destroy(TestUser::select('id')->get()->toArray());
        factory(TestUser::class, 10)->create();
        $users = TestUser::paginate(4);
        $paginated = $repository->paginate(4);
        $this->assertEquals(4, $paginated->count());
        $this->assertEquals($users->toArray(), $paginated->toArray());
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanFilterAndPaginateUsers(TestUserRepository $repository)
    {
        TestUser::destroy(TestUser::select('id')->get()->toArray());
        factory(TestUser::class, 10)->create();
        $users = TestUser::paginate(2);
        $paginated = $repository->where([
            'id' => $users->map(function (TestUser $user) {
                return $user->id;
            })->toArray()
        ])->paginate(4);
        $this->assertEquals(2, $paginated->count());
        $this->assertEquals($users->toArray()['data'], $paginated->toArray()['data']);
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanFindUserById(TestUserRepository $repository)
    {
        $user = factory(TestUser::class)->create();
        $result = $repository->find($user->id);
        $this->assertInstanceOf(TestUser::class, $result);
        $this->assertEquals($user->toArray(), $result->toArray());
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanSaveUser(TestUserRepository $repository)
    {
        $formData = factory(TestUser::class)->make();
        $user = $repository->save($formData->toArray());
        $this->assertInstanceOf(TestUser::class, $user);
        $this->assertNotEmpty($user->id);
        $this->assertEquals($formData->email, $user->email);
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanUpdateUser(TestUserRepository $repository)
    {
        $existing = factory(TestUser::class)->create();
        $update = factory(TestUser::class)->make();
        $user = $repository->update($update->toArray(), $existing->id);
        $this->assertInstanceOf(TestUser::class, $user);
        $this->assertEquals($update->email, $user->email);
    }

    /**
     * @test
     * @depends itCanInstantiate
     * @param TestUserRepository $repository
     */
    public function itCanDestroyUser(TestUserRepository $repository)
    {
        $model = factory(TestUser::class)->create();
        $this->assertTrue($repository->destroy([$model->id]));

        try {
            $repository->find($model->id);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(ModelNotFoundException::class, $exception);
        }
    }
}

