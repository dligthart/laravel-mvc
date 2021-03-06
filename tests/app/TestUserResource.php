<?php

namespace Tychovbh\Tests\Mvc\App;

use Illuminate\Http\Resources\Json\Resource;

class TestUserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'avatar' => $this->avatar
        ];
    }
}
