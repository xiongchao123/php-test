<?php

namespace App\Console\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class GenerateAZGoodsDisplayOrder extends Command
{
    protected function configure()
    {
        $this->setName('generate:goods:display');
        $this->setHelp("generate:goods:display");
        $this->setDescription("generate:goods:display");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("start generate");
        $start = time();

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '33306',
            'database' => 'azazie',
            'username' => 'dbuser0114',
            'password' => 'dbpswd0114',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();

        $capsule->bootEloquent();

        try {
            $goods = $capsule::table('goods')
                ->where('is_on_sale', 1)
                ->where('is_delete', 0)
                ->select('cat_id', 'goods_id')
                ->get();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        foreach ($goods as $item) {
            $colors = $capsule::table('style')
                ->where([
                    'cat_id' => $item->cat_id,
                    'is_show' => 1,
                    'name' => 'color'
                ])
                ->where('parent_id', '>', 0)
                ->pluck('value')
                ->toArray();

            $colors = array_unique($colors);

            array_unshift($colors, 'ALL_COLORS');

            $insert = [];
            foreach ($colors as $color) {
                $insert[] = [
                    'goods_id' => $item->goods_id,
                    'sales_order' => random_int(0, 10000),
                    'sales_order_7_days' => random_int(0, 500),
                    'goods_order' => random_int(0, 1000000),
                    'virtual_sales_order' => random_int(0, 100000),
                    'project_name' => 'azazie',
                    'color' => $color,
                    'effective_cat_id' => $item->cat_id,
                ];
            }

            $capsule::table('goods_display_order')->insert($insert);
        }

        $end = time();

        $executionTime = $end - $start;

        $output->writeln("耗时:{$executionTime}");

        $output->writeln("end generate");
    }
}