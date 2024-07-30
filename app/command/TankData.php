<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


class TankData extends Command
{
    protected static $defaultName = 'tank:data';
    protected static $defaultDescription = 'tank data';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Name description');
    }

    /**
     * 44307+2410 = 46717
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln('Hello tank:data');

        $GameReservationModel = new \app\common\model\reser\GameReservation();
        $GameReserModel = new \app\common\model\reser\GameReser();
        $lists = $GameReservationModel::with(['user' => function($user){
            $user->field('id,email');
        }])->order(['create_time' => 'asc'])
            ->cursor();

        foreach ($lists as $key=>$list) {
            $isReser = $GameReserModel::getFindData(['name' => $list['user']['email']]);
            if ($isReser) {
                if ($list['type'] == 0) {
                    $isReser->update_time = $list['update_time'] + $key;
//                    $GameReserModel::update(['update_time' => $list['update_time'] + $key], ['name' => $list['user']['email']]);
                } else if($list['type'] == 1) {
                    $isReser->update_time = $list['update_time'];
//                    $GameReserModel::update(['update_time' => $list['update_time']], ['name' => $list['user']['email']]);
                }
                $isReser->save();
            } else {
                $GameReserModel::create([
                    'name' => $list['user']['email'],
                    'type' => 2,
                    'status' => $list['type'],
                    'create_time' => $list['create_time'] + $key,
                    'update_time' => $list['update_time'] + $key
                ]);
            }
        }

        return self::SUCCESS;
    }

}
