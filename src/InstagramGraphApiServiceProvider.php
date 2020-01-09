<?php
namespace Nkcx\InstagramGraphApi;
use Illuminate\Support\ServiceProvider;
class InstagramGraphApiServiceProvider extends ServiceProvider {
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
    public function register()
    {
    }
}
?>
