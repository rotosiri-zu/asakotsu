<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    ### 投稿一覧表示機能のテスト ###

    // 未ログイン時
    public function testGuestIndex()
    {
        $response = $this->get(route('articles.index'));

        $response->assertStatus(200)
        ->assertViewIs('articles.index')
        ->assertSee('ユーザー登録')
        ->assertSee('ログイン')
        ->assertSee('かんたんログイン');
    }

    // ログイン時
    public function testAuthIndex()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
        ->get(route('articles.index'));

        $response->assertStatus(200)
        ->assertViewIs('articles.index')
        ->assertSee('投稿する')
        ->assertSee($user->name . 'さん')
        ->assertSee('新規投稿');
    }

    ### 投稿画面表示機能のテスト ###

    // 未ログイン時
    public function testGuestCreate()
    {
        $response = $this->get(route('articles.create'));

        $response->assertRedirect('login');
    }

    // ログイン時
    public function testAuthCreate()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
        ->get(route('articles.create'));

        $response->assertStatus(200)
        ->assertViewIs('articles.create');
    }

    ### 投稿機能のテスト ###

    // 未ログイン時
    public function testGuestStore()
    {
        $response = $this->post(route('articles.store'));

        $response->assertRedirect('login');
    }

    // ログイン時
    public function testAuthStore()
    {
        $user = factory(User::class)->create();

        $body = "テスト本文";
        $user_id = $user->id;

        $response = $this->actingAs($user)
        ->post(route('articles.store',
        [
            'body' => $body,
            'user_id' => $user_id,
            ]
        ));

        // 投稿内容がDBに登録されているかテスト
        $this->assertDatabaseHas('articles', [
            'body' => $body,
            'user_id' => $user_id
        ]);

        $response->assertRedirect(route('articles.index'));
    }

}

