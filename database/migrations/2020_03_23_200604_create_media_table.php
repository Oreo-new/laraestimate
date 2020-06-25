<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->bigIncrements('id');
    
                $table->morphs('model');
                $table->uuid('uuid')->nullable();
                $table->string('collection_name');
                $table->string('name');
                $table->string('file_name');
                $table->string('mime_type')->nullable();
                $table->string('disk');
                $table->string('conversions_disk')->nullable();
                $table->unsignedBigInteger('size');
                $table->longText('manipulations');
                $table->longText('custom_properties');
                $table->longText('responsive_images');
                $table->unsignedInteger('order_column')->nullable();
    
                $table->nullableTimestamps();
            });
        }
    }
}
