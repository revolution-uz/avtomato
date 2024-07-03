<?php

namespace Tests\GraphQL;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GraphQLSchemaIntrospectionTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function common()
    {
        $response = $this->postJson(
            route('graphql.common'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
        $this->assertArrayNotHasKey('errors', $response->json());
    }

    #[Test]
    public function customer()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.customer'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
        $this->assertArrayNotHasKey('errors', $response->json());
    }

    #[Test]
    public function installment()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.installment'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
        $this->assertArrayNotHasKey('errors', $response->json());
    }

    #[Test]
    public function management()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.management'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
        $this->assertArrayNotHasKey('errors', $response->json());
    }

    #[Test]
    public function scoring()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.scoring'),
            ['query' => file_get_contents(__DIR__ . '/introspection_query.gql')]
        );

        $response->assertOk();
        $this->assertArrayNotHasKey('errors', $response->json());
    }
}
