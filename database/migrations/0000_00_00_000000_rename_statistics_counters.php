<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameStatisticsCounters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            // Add new columns
            $table->integer('peak_connections_count')->nullable();
            $table->integer('websocket_messages_count')->nullable();
            $table->integer('api_messages_count')->nullable();
        });

        // Copy data to the new columns
        DB::table('websockets_statistics_entries')->update([
            'peak_connections_count' => DB::raw('peak_connection_count'),
            'websocket_messages_count' => DB::raw('websocket_message_count'),
            'api_messages_count' => DB::raw('api_message_count'),
        ]);

        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn('peak_connection_count');
            $table->dropColumn('websocket_message_count');
            $table->dropColumn('api_message_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            // Add old columns
            $table->integer('peak_connection_count')->nullable();
            $table->integer('websocket_message_count')->nullable();
            $table->integer('api_message_count')->nullable();
        });

        // Copy data back to the old columns
        DB::table('websockets_statistics_entries')->update([
            'peak_connection_count' => DB::raw('peak_connections_count'),
            'websocket_message_count' => DB::raw('websocket_messages_count'),
            'api_message_count' => DB::raw('api_messages_count'),
        ]);

        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn('peak_connections_count');
            $table->dropColumn('websocket_messages_count');
            $table->dropColumn('api_messages_count');
        });
    }
}
