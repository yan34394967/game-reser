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
            ->limit(10)
            ->cursor();

        foreach ($lists as $list) {
            $isReser = $GameReserModel::getFindData(['name' => $list['user']['email']]);
            if ($isReser) {
                $GameReserModel::update(['update_time' => time()], ['name' => $list['user']['email']]);
            } else {
                $GameReserModel::create([
                    'name' => $list['user']['email'],
                    'type' => 2,
                    'status' => $list['type'],
                    'create_time' => $list['create_time'],
                    'update_time' => $list['update_time']
                ]);
            }
        }

        return self::SUCCESS;
    }

}
