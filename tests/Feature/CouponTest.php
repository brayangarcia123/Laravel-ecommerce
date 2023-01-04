<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Coupon;

class CouponTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function coupon_can_be_created()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/coupon',[
            "code"=> "AC-015",
            "start_date"=> "2020-02-28",
            "ending_date"=> "2020-03-30",
            "type_discount"=> 2.9,
            "description"=> "Cupon 15",
            "users_id"=>4,
            "cupons_types_id"=>1
        ]);         

        $response->assertStatus(201);
    }
}
