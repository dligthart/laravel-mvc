<?php

namespace Tychovbh\Tests\Mvc\feature;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Tychovbh\Mvc\Address;
use Tychovbh\Mvc\Country;
use Tychovbh\Mvc\Http\Resources\AddressResource;
use Tychovbh\Mvc\Services\AddressLookup\PdokService;
use Tychovbh\Tests\Mvc\TestCase;

class AddressTest extends TestCase
{
    /**
     * @test
     */
    public function itCanIndex()
    {
        $address = factory(Address::class, 2)->create();
        $test = $this->index('addresses.index', AddressResource::collection($address));
    }

    /**
     * @test
     */
    public function itCanShow()
    {
        $address = factory(Address::class)->create();
        $this->show('addresses.show', AddressResource::make($address));
    }

    /**
     * @test
     */
    public function itCanStore()
    {
        $address = factory(Address::class)->make();
        $address->id = 1;
        $this->store('addresses.store', AddressResource::make($address), $address->toArray());
    }

    /**
     * @test
     */
    public function itCanStoreViaZipcodeAndHouseNumber()
    {
        $address = new Address(['zipcode' => '2352 CZ', 'house_number' => '38']);

        $country = Country::where('name', 'nl')->first();
        $params = $address->toArray();
        $params['country'] = $country->name;

        $address->id = 1;
        $address->country_id = $country->id;
        $address->fill(PdokService::search($address->zipcode, $address->house_number));

        $this->store('addresses.store', AddressResource::make($address), $params);
    }

    /**
     * @test
     */
    public function itCannotStoreZipcodeMissing()
    {
        $address = factory(Address::class)->make(['house_number' => '38']);
        Arr::forget($address, 'zipcode');
        $this->store('addresses.store', AddressResource::make($address), $address->toArray(), 400,
            [
                'zipcode' => [message('field.required', 'zipcode')]
            ]
        );
    }

    /**
     * @test
     */
    public function itCannotStoreHouseNumberMissing()
    {
        $address = factory(Address::class)->make(['zipcode' => '2352 CZ']);
        Arr::forget($address, 'house_number');
        $this->store('addresses.store', AddressResource::make($address), $address->toArray(), 400,
            [
                'house_number' => [message('field.required', 'house_number')]
            ]
        );
    }

    /**
     * @test
     */
    public function itFindsExistingRecordInsteadOfStoring()
    {
        $address = factory(Address::class)->create();
        $this->store('addresses.store', AddressResource::make($address), $address->toArray(), 200);
    }
}
