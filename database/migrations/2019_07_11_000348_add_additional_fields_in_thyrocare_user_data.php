<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsInThyrocareUserData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thyrocare_user_datas', function (Blueprint $table) {
            $table->string('ref_order_id', 255)->after('user_id')->nullable(false)->comment("The Reference Order Number");
            $table->string('email', 255)->after('ref_order_id')->nullable(false)->comment("The user email address");
            $table->string('fasting', 255)->after('email')->nullable(true)->comment("Fasting Type/Non-Fasting Type");
            $table->string('mobile', 255)->after('fasting')->nullable(false)->comment("The user mobile number who has booked");
            $table->string('address', 255)->after('mobile')->nullable(false)->comment("The user address");
            $table->string('booked_by', 255)->after('address')->nullable(false)->comment("The user responsible for order");
            $table->string('product', 255)->after('booked_by')->nullable(false)->comment("Test Product Order Type");
            $table->string('rate', 255)->after('product')->nullable(false)->comment("The user order cost");
            $table->string('service_type', 255)->after('rate')->nullable(false)->comment("The service type chosen by user");
            $table->string('payment_mode', 255)->after('service_type')->nullable(false)->comment("Payment Method");
            $table->string('payment_type', 255)->after('payment_mode')->nullable(false)->comment("Payment Method Type");
            $table->string('order_status', 255)->after('payment_type')->nullable(false)->comment("Status of an order");
            $table->boolean('hard_copy')->after('order_status')->nullable(false)->comment("Report Hard Copy Require or Not");
            $table->mediumText('report_url')->after('hard_copy')->nullable(true)->comment("Report Hard Copy Link");
            $table->dropColumn('order_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thyrocare_user_datas', function (Blueprint $table) {
            $table->dropColumn([
                'ref_order_id', 'email', 'fasting', 'mobile', 'address', 'booked_by',
                'product', 'rate', 'service_type', 'payment_mode',
                'payment_type', 'order_status', 'hard_copy',
            ]);
        });
    }
}
