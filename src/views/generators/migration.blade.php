<?php echo '<?php' ?>

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LottoSetupTables extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        DB::beginTransaction();

        Schema::create('{{ $lottoActivity }}', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('start_time')->comment('活动开始时间')->nullable(false);
            $table->dateTime('draw_time')->comment('活动开奖时间')->nullable(false);
            $table->string('title', 256)->collation('utf8_unicode_ci')->default('')->comment('抽奖标题');
            $table->integer('cost_score')->default('0')->nullable(false)->comment('消耗幸运星');
            $table->integer('sponsor_id')->default('0')->nullable(false)->comment('赞助商ID');
            $table->tinyInteger('join_limit')->default('1')->nullable(false)->comment('参与活动类型, 1表示用户只能参加一次，2表示用户每天可以参加一次');
            $table->integer('join_num')->default('0')->nullable(false)->comment('实际参与人数');
            $table->integer('draw_limit')->default('0')->nullable(false)->comment('最低开奖人数');
            $table->string('description', 512)->collation('utf8_unicode_ci')->default('')->comment('抽奖活动描述');
            $table->integer('show_num')->default('0')->nullable(false)->comment('初始参与人数');
            $table->text('window_config')->nullable(false)->comment('活动跳转配置');
            $table->integer('get_prize_num')->default('0')->nullable(false)->comment('获取奖品的数量');
            $table->integer('sort')->default('0')->nullable(false)->comment('优先级');
            $table->dateTime('create_time')->comment('创建时间')->nullable();
            $table->dateTime('update_time')->comment('更新时间')->nullable();
            $table->dateTime('delete_time')->comment('删除时间')->nullable();
        });

        Schema::create('{{ $lottoActivityConfig }}', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lotto_activity_id')->nullable(false)->comment('抽奖活动ID');
            $table->integer('prize_id')->nullable(false)->comment('抽奖活动奖品ID');
            $table->tinyInteger('prize_num')->default('0')->nullable(false)->comment('奖品数量');
            $table->string('award_desc', 128)->collation('utf8_unicode_ci')->default('')->nullable(false)->comment('奖项名，一等奖，二等奖');
            $table->tinyInteger('show_num')->default('0')->nullable(false)->comment('展示数量');
            $table->tinyInteger('prize_type')->default('1')->nullable(false)->comment('奖品类型 1表示实物，2表示虚拟');
            $table->string('virtual_label', 128)->collation('utf8_unicode_ci')->default('')->nullable(false)->comment('虚拟奖品的标签');
            $table->integer('sort')->default('0')->nullable(false)->comment('优先级');
            $table->dateTime('create_time')->comment('创建时间')->nullable();
            $table->dateTime('update_time')->comment('更新时间')->nullable();
            $table->dateTime('delete_time')->comment('删除时间')->nullable();
            $table->unique(['lotto_activity_id', 'prize_id'], 'activity_unique_key');
        });

        Schema::create('{{ $lottoPrize }}', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('奖品名称');
            $table->string('image_url', 256)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('奖品图片地址');
            $table->integer('sku_id')->nullable(false)->default('0')->comment('奖品单品ID');
            $table->string('sku_name', 128)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('奖品单品名称');
            $table->dateTime('create_time')->comment('创建时间')->nullable();
            $table->dateTime('update_time')->comment('更新时间')->nullable();
            $table->dateTime('delete_time')->comment('删除时间')->nullable();
            $table->unique(['sku_id'], 'sku_unique_key');
        });

        Schema::create('{{ $lottoActivityRecord }}', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lotto_activity_id')->nullable(false)->comment('抽奖活动ID')->index('lotto_index');
            $table->integer('uid')->nullable(false)->comment('参与用户ID')->index('uid_index');
            $table->integer('cost_score')->default('0')->nullable(false)->comment('消耗幸运星');
            $table->tinyInteger('status')->default('0')->nullable(false)->comment('中奖状态，0表示未中奖，1表示中奖');
            $table->dateTime('create_time')->comment('创建时间')->nullable();
        });

        Schema::create('{{ $lottoRewardRecord }}', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lotto_activity_id')->nullable(false)->comment('抽奖活动ID')->index('lotto_index');
            $table->integer('record_id')->nullable(false)->default('0')->comment('抽奖记录的ID');
            $table->integer('project_id')->nullable(false)->comment('奖项ID，对应lotto_prize_config的ID');
            $table->string('project_name', 128)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('奖项名称');
            $table->integer('prize_id')->nullable(false)->comment('抽奖活动奖品ID');
            $table->integer('uid')->nullable(false)->comment('参与用户ID')->index('uid_index');
            $table->string('username', 50)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('中奖姓名');
            $table->string('nickname', 50)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('中奖用户昵称');
            $table->string('avatar', 256)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('头像');
            $table->string('mobile', 18)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('手机号');
            $table->string('address', 256)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('地址');
            $table->tinyInteger('award_type')->default('1')->nullable(false)->comment('类型 1表示实物，2表示虚拟');
            $table->integer('award_value')->nullable(false)->default('0')->comment('虚拟中奖金额');
            $table->string('fast_num', 50)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('实物发货的快递单号');
            $table->tinyInteger('fast_status')->default('0')->nullable(false)->comment('中奖商品发放状态 0表示未发货，1表示已发货');
            $table->dateTime('create_time')->comment('创建时间')->nullable();
            $table->dateTime('update_time')->comment('更新时间')->nullable();
        });

        Schema::create('{{ $lottoSponsor }}', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('赞助商名称');
            $table->string('icon_url', 256)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('图标icon');
            $table->string('app_id', 64)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('赞助商小程序APP_ID');
            $table->string('path_url', 128)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('跳转下的url');
            $table->string('content', 512)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('小程序介绍内容');
            $table->string('share_text', 512)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('分享文案');
            $table->tinyInteger('status')->nullable(false)->default('0')->comment('是否开启 1表示开启，0表示关闭');
            $table->tinyInteger('self_support')->nullable(false)->default('0')->comment('是否开启 1表示自营，0表示非自营');
            $table->string('description', 512)->collation('utf8_unicode_ci')->nullable(false)->default('')->comment('赞助商介绍');
            $table->dateTime('create_time')->comment('创建时间')->nullable();
            $table->dateTime('update_time')->comment('更新时间')->nullable();
            $table->unique(['app_id'], 'app_unique_key');
        });

        DB::commit();
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('{{ $lottoActivity }}');
        Schema::dropIfExists('{{ $lottoActivityConfig }}');
        Schema::dropIfExists('{{ $lottoPrize }}');
        Schema::dropIfExists('{{ $lottoActivityRecord }}');
        Schema::dropIfExists('{{ $lottoRewardRecord }}');
        Schema::dropIfExists('{{ $lottoSponsor }}');
    }
}
