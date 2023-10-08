<?php

namespace Tests;

class TestResponse extends \Illuminate\Testing\TestResponse
{
    public function assertOk()
    {
        $this->assertStatus(200);
        $this->assertJsonFragment(['success' => true]);
    }

    public function assertError()
    {
        $this->assertStatus(200);
        $this->assertJsonFragment(['success' => false]);
    }
}
