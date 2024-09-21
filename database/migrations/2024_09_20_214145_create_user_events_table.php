<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEventsTable extends Migration
{
    public function up()
    {
        Schema::create('user_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('event_id')->unique(); // Store the GitHub event ID
            $table->string('repo_name'); // Store the repository name
            $table->text('title'); // Store commit messages or titles here
            $table->longText('description')->nullable(); // Store descriptions (PR or Issue bodies)
            $table->string('type'); // Event type (PushEvent, PullRequestEvent, IssuesEvent)
            $table->timestamp('event_date'); // Event date from GitHub
            $table->timestamps(); // For created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_events');
    }
}
