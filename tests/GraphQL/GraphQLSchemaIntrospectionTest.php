<?php

namespace Tests\GraphQL;

use Tests\TestCase;

class GraphQLSchemaIntrospectionTest extends TestCase
{
    protected bool $tenancy = true;

    /**
     * @test
     */
    public function common()
    {
        $response = $this->postJson(
            route('graphql.common'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
    }

    /**
     * @test
     */
    public function customer()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.customer'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
    }

    /**
     * @test
     */
    public function installment()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.installment'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
    }

    /**
     * @test
     */
    public function management()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.management'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
    }

    /**
     * @test
     */
    public function scoring()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.scoring'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
    }
}
