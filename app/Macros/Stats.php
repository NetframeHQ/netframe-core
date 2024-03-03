<?php

/**
 *  Compute stats increse between 2 periods
 */
HTML::macro('statsIncrease', function ($newPeriod, $oldPeriod) {
    if ($newPeriod == $oldPeriod && $oldPeriod == 0) {
        $increase = '+ 0';
        $increaseColor = 'text-warning';
    } elseif ($newPeriod == $oldPeriod) {
        $increase = '+ 0';
        $increaseColor = 'text-warning';
    } elseif ($oldPeriod == 0) {
        $increase = '+ ' . 100;
        $increaseColor = 'text-success';
    } elseif ($newPeriod > $oldPeriod) {
        $increase = '+ ' . round((($newPeriod - $oldPeriod) / $oldPeriod) * 100, 2);
        $increaseColor = 'text-success';
    } elseif ($newPeriod < $oldPeriod) {
        $increase = '- ' . abs(round((($newPeriod - $oldPeriod) / $oldPeriod) * 100, 2));
        $increaseColor = 'text-danger';
    }

    $tpl = [
        'increase' => $increase,
        'increaseColor' => $increaseColor,
    ];

    return view('macros.stats.increase', $tpl)->render();
});
