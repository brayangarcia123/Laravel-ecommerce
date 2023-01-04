<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Category;


class CategoryTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    /** @test */
    public function category_can_be_created(){
        $this->withoutExceptionHandling();

        $response = $this->post('api/categories',[
            'name' => 'Name Test',
            'description' => 'Description Test'
        ]);

        $response->assertStatus(201);


        
    }
}
